<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PayDunyaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PaymentController extends Controller
{
    /**
     * Afficher la page de paiement.
     */
    public function create(Booking $booking)
    {
        $booking->load([
            'flight.airline',
            'flight.departureAirport',
            'flight.arrivalAirport',
            'passengers',
            'bookingBaggages.baggageOption',
        ]);

        return view('payments.create', compact('booking'));
    }

    /**
     * Lancer le paiement.
     */
    public function store(
        Request $request,
        Booking $booking,
        PayDunyaService $payDunya
    ) {
        $request->validate([
            'payment_method' => [
                'required',
                'in:wave,orange_money,wizall,card',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Charger les données de la réservation
        |--------------------------------------------------------------------------
        */

        $booking->load([
            'flight.airline',
            'flight.departureAirport',
            'flight.arrivalAirport',
            'passengers',
            'user',
            'bookingBaggages.baggageOption',
        ]);

        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status === 'confirmed') {
            return redirect()
                ->route('bookings.show', $booking)
                ->with('info', 'Cette réservation est déjà confirmée.');
        }

        /*
        |--------------------------------------------------------------------------
        | Informations client
        |--------------------------------------------------------------------------
        */

        $passenger = $booking->passengers->first();

        if (!$passenger) {
            return back()->withErrors([
                'payment' => 'Aucun passager n’est associé à cette réservation.',
            ]);
        }

        $fullName = trim(
            ($passenger->first_name ?? '') . ' ' .
            ($passenger->last_name ?? '')
        );

        $email = $booking->user->email
            ?? $passenger->email
            ?? null;

        $phone = $passenger->phone
            ?? $booking->user->phone
            ?? null;

        if (!$email || !$phone) {
            return back()->withErrors([
                'payment' =>
                    'Les informations e-mail et téléphone du client sont nécessaires pour le paiement.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Méthode de paiement
        |--------------------------------------------------------------------------
        */

        $paymentMethod = $request->input('payment_method');

        /*
        |--------------------------------------------------------------------------
        | Création de la facture PayDunya
        |--------------------------------------------------------------------------
        */

        try {
            $invoice = [
                'invoice' => [
                    'total_amount' => (float) $booking->total_amount,
                    'description' =>
                        'Paiement réservation ' .
                        $booking->booking_reference,
                ],

                'store' => [
                    'name' => 'Yada Voyage',
                    'tagline' => 'Votre voyage, simplement.',
                    'website_url' => config('app.url'),
                ],

                'actions' => [
                    'callback_url' => route('payments.callback'),
                    'return_url' => route('payments.return'),
                    'cancel_url' => route('payments.cancel'),
                ],

                'custom_data' => [
                    'booking_id' => $booking->id,
                    'booking_reference' => $booking->booking_reference,
                    'payment_method' => $paymentMethod,
                ],

                'customer' => [
                    'name' => $fullName,
                    'email' => $email,
                    'phone' => $phone,
                ],

                'items' => [
                    [
                        'name' =>
                            'Billet ' .
                            ($booking->flight->flight_number ?? 'Vol'),

                        'quantity' =>
                            $booking->passengers->count(),

                        'unit_price' =>
                            (float) $booking->flight->price,

                        'total_price' =>
                            (float) $booking->total_amount,
                    ],
                ],
            ];

            /*
            |--------------------------------------------------------------------------
            | Création de la facture PayDunya
            |--------------------------------------------------------------------------
            */

            $result = $payDunya->createInvoice($invoice);

            $invoiceToken = $result['token'] ?? null;

            if (!$invoiceToken) {
                throw new RuntimeException(
                    'PayDunya n’a pas retourné de token de facture.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Création du paiement dans notre base
            |--------------------------------------------------------------------------
            */

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount,
                'payment_method' => $paymentMethod,
                'transaction_id' => $invoiceToken,
                'status' => 'pending',
            ]);

            /*
            |--------------------------------------------------------------------------
            | WAVE
            |--------------------------------------------------------------------------
            */

            if ($paymentMethod === 'wave') {
                $result = $payDunya->payWave(
                    $fullName,
                    $email,
                    $phone,
                    $invoiceToken
                );

                /*
                |--------------------------------------------------------------------------
                | SANDBOX
                |--------------------------------------------------------------------------
                |
                | En mode test, PayDunya confirme directement le paiement.
                | Il n'y a pas de redirection vers l'application Wave.
                |
                */

                if (config('paydunya.mode') === 'test') {
                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]);

                    $booking->update([
                        'status' => 'confirmed',
                    ]);

                    return redirect()
                        ->route('bookings.show', $booking)
                        ->with(
                            'success',
                            'Paiement sandbox effectué avec succès.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | PRODUCTION
                |--------------------------------------------------------------------------
                */

                if (!isset($result['url'])) {
                    throw new RuntimeException(
                        'Impossible de lancer le paiement Wave. Vérifiez votre configuration PayDunya.'
                    );
                }

                return redirect()->away($result['url']);
            }

            /*
            |--------------------------------------------------------------------------
            | ORANGE MONEY
            |--------------------------------------------------------------------------
            */

            if ($paymentMethod === 'orange_money') {
                $result = $payDunya->payOrangeMoney(
                    $fullName,
                    $email,
                    $phone,
                    $invoiceToken
                );

                /*
                |--------------------------------------------------------------------------
                | SANDBOX
                |--------------------------------------------------------------------------
                */

                if (config('paydunya.mode') === 'test') {
                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]);

                    $booking->update([
                        'status' => 'confirmed',
                    ]);

                    return redirect()
                        ->route('bookings.show', $booking)
                        ->with(
                            'success',
                            'Paiement sandbox effectué avec succès.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | PRODUCTION
                |--------------------------------------------------------------------------
                */

                $paymentUrl =
                    $result['url']
                    ?? $result['other_url']['om_url']
                    ?? null;

                if (!$paymentUrl) {
                    throw new RuntimeException(
                        'Impossible de lancer le paiement Orange Money.'
                    );
                }

                return redirect()->away($paymentUrl);
            }

            /*
            |--------------------------------------------------------------------------
            | WIZALL
            |--------------------------------------------------------------------------
            */

            if ($paymentMethod === 'wizall') {
                $result = $payDunya->payWizall(
                    $fullName,
                    $email,
                    $phone,
                    $invoiceToken
                );

                /*
                |--------------------------------------------------------------------------
                | SANDBOX
                |--------------------------------------------------------------------------
                */

                if (config('paydunya.mode') === 'test') {
                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]);

                    $booking->update([
                        'status' => 'confirmed',
                    ]);

                    return redirect()
                        ->route('bookings.show', $booking)
                        ->with(
                            'success',
                            'Paiement sandbox effectué avec succès.'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | PRODUCTION
                |--------------------------------------------------------------------------
                */

                $transactionId =
                    $result['data']['TransactionID']
                    ?? null;

                if ($transactionId) {
                    $payment->update([
                        'transaction_id' => $transactionId,
                    ]);
                }

                return redirect()
                    ->route('bookings.show', $booking)
                    ->with(
                        'success',
                        'La demande de paiement Wizall a été envoyée. La confirmation sera ajoutée après validation.'
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | CARTE BANCAIRE
            |--------------------------------------------------------------------------
            */

            if ($paymentMethod === 'card') {
                $checkoutUrl =
                    $result['response_text']
                    ?? null;

                if (!$checkoutUrl) {
                    throw new RuntimeException(
                        'Impossible de créer le paiement par carte.'
                    );
                }

                return redirect()->away($checkoutUrl);
            }

            throw new RuntimeException(
                'Méthode de paiement non reconnue.'
            );
        } catch (\Throwable $e) {
            Log::error(
                'Erreur paiement PayDunya',
                [
                    'booking_id' => $booking->id,
                    'payment_method' => $paymentMethod,
                    'message' => $e->getMessage(),
                ]
            );

            return back()->withErrors([
                'payment' =>
                    $e->getMessage(),
            ]);
        }
    }

    /**
     * Retour de PayDunya après paiement.
     */
    public function return(
        Request $request,
        PayDunyaService $payDunya
    ) {
        $token = $request->query('token');

        if (!$token) {
            return redirect()
                ->route('dashboard')
                ->withErrors([
                    'payment' =>
                        'Token de paiement manquant.',
                ]);
        }

        try {
            $result = $payDunya->confirmInvoice($token);

            $payment = Payment::where(
                'transaction_id',
                $token
            )->first();

            if (!$payment) {
                return redirect()
                    ->route('dashboard')
                    ->withErrors([
                        'payment' =>
                            'Paiement introuvable.',
                    ]);
            }

            $booking = $payment->booking;

            $status = strtolower(
                $result['status'] ?? ''
            );

            if ($status === 'completed') {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                $booking->update([
                    'status' => 'confirmed',
                ]);

                return redirect()
                    ->route('bookings.show', $booking)
                    ->with(
                        'success',
                        'Paiement effectué avec succès.'
                    );
            }

            if ($status === 'cancelled') {
                $payment->update([
                    'status' => 'cancelled',
                ]);

                return redirect()
                    ->route('bookings.show', $booking)
                    ->with(
                        'error',
                        'Le paiement a été annulé.'
                    );
            }

            return redirect()
                ->route('bookings.show', $booking)
                ->with(
                    'info',
                    'Le paiement est toujours en attente de confirmation.'
                );
        } catch (\Throwable $e) {
            Log::error(
                'Erreur retour PayDunya',
                [
                    'token' => $token,
                    'message' => $e->getMessage(),
                ]
            );

            return redirect()
                ->route('dashboard')
                ->withErrors([
                    'payment' =>
                        'Impossible de vérifier le paiement PayDunya.',
                ]);
        }
    }

    /**
     * Annulation du paiement.
     */
    public function cancel(
        Request $request
    ) {
        $token = $request->query('token');

        if ($token) {
            $payment = Payment::where(
                'transaction_id',
                $token
            )->first();

            if ($payment) {
                $payment->update([
                    'status' => 'cancelled',
                ]);

                $payment->booking->update([
                    'status' => 'cancelled',
                ]);
            }
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'info',
                'Le paiement a été annulé.'
            );
    }

    /**
     * Callback PayDunya.
     */
    public function callback(
        Request $request
    ) {
        try {
            $data = $request->input('data', []);

            /*
            |--------------------------------------------------------------------------
            | Vérification de la signature PayDunya
            |--------------------------------------------------------------------------
            */

            $expectedHash = hash(
                'sha512',
                config('paydunya.master_key')
            );

            if (
                isset($data['hash']) &&
                !hash_equals(
                    $expectedHash,
                    $data['hash']
                )
            ) {
                Log::warning(
                    'Callback PayDunya avec signature invalide.'
                );

                return response()->json([
                    'success' => false,
                    'message' => 'Signature invalide.',
                ], 403);
            }

            /*
            |--------------------------------------------------------------------------
            | Token de la facture
            |--------------------------------------------------------------------------
            */

            $token =
                $data['invoice']['token']
                ?? null;

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token manquant.',
                ], 400);
            }

            $payment = Payment::where(
                'transaction_id',
                $token
            )->first();

            if (!$payment) {
                Log::warning(
                    'Paiement PayDunya introuvable.',
                    [
                        'token' => $token,
                    ]
                );

                return response()->json([
                    'success' => false,
                    'message' => 'Paiement introuvable.',
                ], 404);
            }

            $status = strtolower(
                $data['status'] ?? ''
            );

            /*
            |--------------------------------------------------------------------------
            | Paiement réussi
            |--------------------------------------------------------------------------
            */

            if ($status === 'completed') {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                $payment->booking->update([
                    'status' => 'confirmed',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Paiement annulé
            |--------------------------------------------------------------------------
            */

            elseif ($status === 'cancelled') {
                $payment->update([
                    'status' => 'cancelled',
                ]);

                $payment->booking->update([
                    'status' => 'cancelled',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Paiement échoué
            |--------------------------------------------------------------------------
            */

            elseif ($status === 'failed') {
                $payment->update([
                    'status' => 'failed',
                ]);
            }

            return response()->json([
                'success' => true,
            ]);
        } catch (\Throwable $e) {
            Log::error(
                'Erreur callback PayDunya',
                [
                    'message' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur.',
            ], 500);
        }
    }
}