<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PayDunyaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PAGE DE PAIEMENT
    |--------------------------------------------------------------------------
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

        return view(
            'payments.create',
            compact('booking')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LANCEMENT DU PAIEMENT
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Booking $booking,
        PayDunyaService $payDunya
    ) {
        $validated = $request->validate([
            'payment_method' => [
                'required',
                'in:wave,orange_money,wizall,card',
            ],
        ], [
            'payment_method.required' =>
                'Veuillez choisir un moyen de paiement.',

            'payment_method.in' =>
                'Le moyen de paiement sélectionné n’est pas valide.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Charger les informations nécessaires
        |--------------------------------------------------------------------------
        */

        $booking->load([
            'flight.airline',
            'passengers',
            'user',
        ]);


        $passenger = $booking->passengers->first();


        if (!$passenger) {
            return back()->withErrors([
                'payment' =>
                    'Aucun passager associé à cette réservation.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Vérifier le téléphone
        |--------------------------------------------------------------------------
        */

        if (!$passenger->phone) {
            return back()->withErrors([
                'payment' =>
                    'Le numéro de téléphone du passager est obligatoire pour effectuer le paiement.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Nom du client
        |--------------------------------------------------------------------------
        */

        $fullName =
            trim(
                $passenger->first_name
                . ' '
                . $passenger->last_name
            );


        /*
        |--------------------------------------------------------------------------
        | Email
        |--------------------------------------------------------------------------
        */

        $email =
            $booking->user->email
            ?? $passenger->email
            ?? 'client@yadavoyage.com';


        /*
        |--------------------------------------------------------------------------
        | Numéro de téléphone
        |--------------------------------------------------------------------------
        */

        $phone =
            preg_replace(
                '/\s+/',
                '',
                $passenger->phone
            );


        /*
        |--------------------------------------------------------------------------
        | Création de la facture PayDunya
        |--------------------------------------------------------------------------
        */

        $invoice = [

            'invoice' => [

                'total_amount' =>
                    (float) $booking->total_amount,

                'description' =>
                    'Paiement de la réservation '
                    . $booking->booking_reference,

                'customer' => [

                    'name' =>
                        $fullName,

                    'email' =>
                        $email,

                    'phone' =>
                        $phone,
                ],

                'items' => [

                    'item_0' => [

                        'name' =>
                            'Billet d’avion '
                            . $booking->flight->flight_number,

                        'quantity' =>
                            $booking->number_of_passengers,

                        'unit_price' =>
                            (string)
                            $booking->flight->price,

                        'total_price' =>
                            (string)
                            $booking->total_amount,

                        'description' =>
                            'Réservation de vol Yada Voyage',
                    ],
                ],
            ],

            'store' => [

                'name' =>
                    'Yada Voyage',
            ],

            'custom_data' => [

                'booking_id' =>
                    $booking->id,

                'booking_reference' =>
                    $booking->booking_reference,

                'payment_method' =>
                    $validated['payment_method'],
            ],

            'actions' => [

                'return_url' =>
                    route('payments.return'),

                'cancel_url' =>
                    route('payments.cancel'),

                'callback_url' =>
                    route('payments.callback'),
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | Demander la création de la facture
        |--------------------------------------------------------------------------
        */

        try {

            $result =
                $payDunya->createInvoice(
                    $invoice
                );

        } catch (\Throwable $e) {

            Log::error(
                'Erreur création facture PayDunya',
                [
                    'booking_id' =>
                        $booking->id,

                    'error' =>
                        $e->getMessage(),
                ]
            );

            return back()->withErrors([
                'payment' =>
                    'Impossible de créer le paiement PayDunya pour le moment. Vérifiez votre configuration PayDunya.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Récupérer le token PayDunya
        |--------------------------------------------------------------------------
        */

        $token =
            $result['token']
            ?? null;


        if (!$token) {

            return back()->withErrors([
                'payment' =>
                    'PayDunya n’a pas retourné de token de paiement.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Enregistrer le paiement en base
        |--------------------------------------------------------------------------
        */

        $payment = Payment::create([

            'booking_id' =>
                $booking->id,

            'amount' =>
                $booking->total_amount,

            'payment_method' =>
                $validated['payment_method'],

            'transaction_id' =>
                $token,

            'status' =>
                'pending',
        ]);


        /*
        |--------------------------------------------------------------------------
        | WAVE
        |--------------------------------------------------------------------------
        */

        if (
            $validated['payment_method']
            === 'wave'
        ) {

            try {

                $result =
                    $payDunya->payWave(
                        $fullName,
                        $email,
                        $phone,
                        $token
                    );


                $url =
                    $result['url']
                    ?? null;


                if (!$url) {

                    throw new \RuntimeException(
                        'URL Wave absente de la réponse PayDunya.'
                    );
                }


                return redirect()->away(
                    $url
                );

            } catch (\Throwable $e) {

                Log::error(
                    'Erreur paiement Wave',
                    [
                        'booking_id' =>
                            $booking->id,

                        'error' =>
                            $e->getMessage(),
                    ]
                );

                $payment->update([
                    'status' => 'failed',
                ]);

                return back()->withErrors([
                    'payment' =>
                        'Impossible de lancer le paiement Wave. Vérifiez votre configuration PayDunya.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ORANGE MONEY
        |--------------------------------------------------------------------------
        */

        if (
            $validated['payment_method']
            === 'orange_money'
        ) {

            try {

                $result =
                    $payDunya->payOrangeMoney(
                        $fullName,
                        $email,
                        $phone,
                        $token
                    );


                /*
                |--------------------------------------------------------------------------
                | URL principale Orange Money
                |--------------------------------------------------------------------------
                */

                $url =
                    $result['url']
                    ?? null;


                /*
                |--------------------------------------------------------------------------
                | URL directe application Orange Money
                |--------------------------------------------------------------------------
                */

                if (!$url) {

                    $url =
                        $result['other_url']['om_url']
                        ?? null;
                }


                if (!$url) {

                    throw new \RuntimeException(
                        'URL Orange Money absente de la réponse PayDunya.'
                    );
                }


                return redirect()->away(
                    $url
                );

            } catch (\Throwable $e) {

                Log::error(
                    'Erreur paiement Orange Money',
                    [
                        'booking_id' =>
                            $booking->id,

                        'error' =>
                            $e->getMessage(),
                    ]
                );

                $payment->update([
                    'status' => 'failed',
                ]);

                return back()->withErrors([
                    'payment' =>
                        'Impossible de lancer le paiement Orange Money. Vérifiez votre configuration PayDunya.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | WIZALL
        |--------------------------------------------------------------------------
        |
        | Wizall nécessite une étape supplémentaire :
        | PayDunya retourne un TransactionID puis demande
        | un code d'autorisation pour confirmer le paiement.
        |
        */

        if (
            $validated['payment_method']
            === 'wizall'
        ) {

            try {

                $result =
                    $payDunya->payWizall(
                        $fullName,
                        $email,
                        $phone,
                        $token
                    );


                $transactionId =
                    $result['data']['TransactionID']
                    ?? null;


                if (!$transactionId) {

                    throw new \RuntimeException(
                        'TransactionID Wizall absent de la réponse PayDunya.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | Pour l'instant, on conserve le TransactionID.
                |--------------------------------------------------------------------------
                */

                $payment->update([
                    'transaction_id' =>
                        $transactionId,
                ]);


                return redirect()
                    ->route(
                        'payments.create',
                        $booking
                    )
                    ->with(
                        'success',
                        'La demande de paiement Wizall a été envoyée. L’étape de confirmation sera ajoutée ensuite.'
                    );

            } catch (\Throwable $e) {

                Log::error(
                    'Erreur paiement Wizall',
                    [
                        'booking_id' =>
                            $booking->id,

                        'error' =>
                            $e->getMessage(),
                    ]
                );

                $payment->update([
                    'status' => 'failed',
                ]);

                return back()->withErrors([
                    'payment' =>
                        'Impossible de lancer le paiement Wizall. Vérifiez votre configuration PayDunya.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CARTE BANCAIRE
        |--------------------------------------------------------------------------
        |
        | Pour la carte, on utilise actuellement le checkout
        | classique PayDunya.
        |
        */

        if (
            $validated['payment_method']
            === 'card'
        ) {

            $checkoutUrl =
                $result['response_text']
                ?? null;


            if (!$checkoutUrl) {

                $payment->update([
                    'status' => 'failed',
                ]);

                return back()->withErrors([
                    'payment' =>
                        'PayDunya n’a pas retourné de lien de paiement par carte.',
                ]);
            }


            return redirect()->away(
                $checkoutUrl
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Sécurité
        |--------------------------------------------------------------------------
        */

        return back()->withErrors([
            'payment' =>
                'Moyen de paiement non pris en charge.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | RETOUR PAYDUNYA
    |--------------------------------------------------------------------------
    */

    public function return(
        Request $request,
        PayDunyaService $payDunya
    ) {

        $token =
            $request->query('token');


        if (!$token) {

            return redirect()
                ->route('flights.index')
                ->withErrors([
                    'payment' =>
                        'Token de paiement introuvable.',
                ]);
        }


        try {

            $result =
                $payDunya->confirmInvoice(
                    $token
                );

        } catch (\Throwable $e) {

            Log::error(
                'Erreur confirmation PayDunya',
                [
                    'token' =>
                        $token,

                    'error' =>
                        $e->getMessage(),
                ]
            );

            return redirect()
                ->route('flights.index')
                ->withErrors([
                    'payment' =>
                        'Impossible de vérifier le paiement.',
                ]);
        }


        $status =
            $result['invoice']['status']
            ?? null;


        $payment =
            Payment::where(
                'transaction_id',
                $token
            )->first();


        if (!$payment) {

            return redirect()
                ->route('flights.index')
                ->withErrors([
                    'payment' =>
                        'Paiement introuvable.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Paiement confirmé
        |--------------------------------------------------------------------------
        */

        if (
            $status === 'completed'
        ) {

            $payment->update([

                'status' =>
                    'completed',

                'paid_at' =>
                    now(),
            ]);


            $payment->booking->update([
                'status' =>
                    'confirmed',
            ]);


            return redirect()
                ->route(
                    'payments.create',
                    $payment->booking
                )
                ->with(
                    'success',
                    'Paiement confirmé avec succès.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Paiement annulé
        |--------------------------------------------------------------------------
        */

        if (
            $status === 'cancelled'
        ) {

            $payment->update([
                'status' =>
                    'cancelled',
            ]);


            return redirect()
                ->route(
                    'payments.create',
                    $payment->booking
                )
                ->withErrors([
                    'payment' =>
                        'Le paiement a été annulé.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Paiement encore en attente
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'payments.create',
                $payment->booking
            )
            ->withErrors([
                'payment' =>
                    'Le paiement est encore en attente de confirmation.',
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ANNULATION
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Request $request
    ) {

        $token =
            $request->query('token');


        $payment =
            $token
                ? Payment::where(
                    'transaction_id',
                    $token
                )->first()
                : null;


        if ($payment) {

            $payment->update([
                'status' =>
                    'cancelled',
            ]);


            $payment->booking->update([
                'status' =>
                    'cancelled',
            ]);


            return redirect()
                ->route(
                    'payments.create',
                    $payment->booking
                )
                ->withErrors([
                    'payment' =>
                        'Le paiement a été annulé.',
                ]);
        }


        return redirect()
            ->route('flights.index')
            ->withErrors([
                'payment' =>
                    'Le paiement a été annulé.',
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CALLBACK PAYDUNYA
    |--------------------------------------------------------------------------
    */

    public function callback(
        Request $request
    ) {

        $data =
            $request->input(
                'data',
                []
            );


        if (!is_array($data)) {

            return response()->json([
                'message' =>
                    'Données PayDunya invalides.',
            ], 400);
        }


        /*
        |--------------------------------------------------------------------------
        | Vérification du hash
        |--------------------------------------------------------------------------
        */

        $receivedHash =
            $data['hash']
            ?? null;


        if (!$receivedHash) {

            return response()->json([
                'message' =>
                    'Hash PayDunya manquant.',
            ], 400);
        }


        $expectedHash =
            hash(
                'sha512',
                config(
                    'paydunya.master_key'
                )
            );


        if (
            !hash_equals(
                $expectedHash,
                $receivedHash
            )
        ) {

            return response()->json([
                'message' =>
                    'Callback PayDunya non authentifié.',
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | Récupération des données
        |--------------------------------------------------------------------------
        */

        $status =
            $data['status']
            ?? null;


        $token =
            $data['invoice']['token']
            ?? null;


        if (!$token) {

            return response()->json([
                'message' =>
                    'Token de paiement manquant.',
            ], 400);
        }


        /*
        |--------------------------------------------------------------------------
        | Recherche du paiement
        |--------------------------------------------------------------------------
        */

        $payment =
            Payment::where(
                'transaction_id',
                $token
            )->first();


        if (!$payment) {

            return response()->json([
                'message' =>
                    'Paiement introuvable.',
            ], 404);
        }


        /*
        |--------------------------------------------------------------------------
        | Paiement terminé
        |--------------------------------------------------------------------------
        */

        if (
            $status === 'completed'
        ) {

            $payment->update([

                'status' =>
                    'completed',

                'paid_at' =>
                    now(),
            ]);


            $payment->booking->update([
                'status' =>
                    'confirmed',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Paiement annulé
        |--------------------------------------------------------------------------
        */

        elseif (
            $status === 'cancelled'
        ) {

            $payment->update([
                'status' =>
                    'cancelled',
            ]);


            $payment->booking->update([
                'status' =>
                    'cancelled',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Paiement échoué
        |--------------------------------------------------------------------------
        */

        elseif (
            $status === 'failed'
        ) {

            $payment->update([
                'status' =>
                    'failed',
            ]);


            $payment->booking->update([
                'status' =>
                    'cancelled',
            ]);
        }


        return response()->json([
            'message' =>
                'Callback PayDunya traité avec succès.',
        ]);
    }
}