<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BaggageOption;
use App\Models\Flight;
use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Afficher le formulaire de réservation.
     */
    public function create(Flight $flight)
    {
        // Vérifier que le vol est disponible
        abort_unless($flight->is_active, 404);

        // Vérifier qu'il reste des places
        abort_if($flight->available_seats < 1, 404);

        $flight->load([
            'airline',
            'departureAirport',
            'arrivalAirport',
        ]);

        $baggageOptions = BaggageOption::where('is_active', true)
            ->orderBy('is_included', 'desc')
            ->orderBy('weight_kg')
            ->get();

        return view('bookings.create', compact(
            'flight',
            'baggageOptions'
        ));
    }

    /**
     * Enregistrer une réservation.
     */
    public function store(Request $request, Flight $flight)
    {
        // Vérifier que le vol est actif
        abort_unless($flight->is_active, 404);

        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
            ],

            'nationality' => [
                'nullable',
                'string',
                'max:100',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'passport_number' => [
                'nullable',
                'string',
                'max:100',
                'unique:passengers,passport_number',
            ],

            'passport_expiry' => [
                'nullable',
                'date',
                'after:today',
            ],

            'baggage' => [
                'nullable',
                'array',
            ],

            'baggage.*' => [
                'nullable',
                'integer',
                'min:0',
                'max:5',
            ],
        ]);

        // Vérifier une nouvelle fois les places disponibles
        if ($flight->available_seats < 1) {
            return back()
                ->withErrors([
                    'flight' => 'Ce vol n\'a plus de place disponible.',
                ])
                ->withInput();
        }

        try {
            $booking = DB::transaction(function () use (
                $validated,
                $flight
            ) {

                /*
                |--------------------------------------------------------------------------
                | Création du passager
                |--------------------------------------------------------------------------
                */

                $passenger = Passenger::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'nationality' => $validated['nationality'] ?? null,
                    'passport_number' => $validated['passport_number'] ?? null,
                    'passport_expiry' => $validated['passport_expiry'] ?? null,
                    'phone' => $validated['phone'],
                    'email' => $validated['email'] ?? auth()->user()->email,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Création de la réservation
                |--------------------------------------------------------------------------
                |
                | IMPORTANT :
                | On utilise maintenant l'utilisateur connecté.
                |
                */

                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'flight_id' => $flight->id,
                    'booking_reference' => 'YADA-' . strtoupper(Str::random(8)),
                    'number_of_passengers' => 1,
                    'total_amount' => $flight->price,
                    'status' => 'pending',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Associer le passager à la réservation
                |--------------------------------------------------------------------------
                */

                $booking->passengers()->attach($passenger->id);

                /*
                |--------------------------------------------------------------------------
                | Calcul des bagages
                |--------------------------------------------------------------------------
                */

                $totalAmount = (float) $flight->price;

                if (!empty($validated['baggage'])) {

                    foreach ($validated['baggage'] as $baggageId => $quantity) {

                        $quantity = (int) $quantity;

                        if ($quantity <= 0) {
                            continue;
                        }

                        $baggageOption = BaggageOption::where(
                            'id',
                            $baggageId
                        )
                            ->where('is_active', true)
                            ->first();

                        if (!$baggageOption) {
                            continue;
                        }

                        $unitPrice = (float) $baggageOption->price;

                        $booking->bookingBaggages()->create([
                            'baggage_option_id' => $baggageOption->id,
                            'quantity' => $quantity,
                            'unit_price' => $unitPrice,
                        ]);

                        $totalAmount += $unitPrice * $quantity;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Mise à jour du montant total
                |--------------------------------------------------------------------------
                */

                $booking->update([
                    'total_amount' => $totalAmount,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Réduire le nombre de places disponibles
                |--------------------------------------------------------------------------
                */

                $flight->decrement('available_seats');

                return $booking;
            });

            /*
            |--------------------------------------------------------------------------
            | Redirection vers le paiement
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('payments.create', $booking)
                ->with(
                    'success',
                    'Votre réservation a été créée avec succès.'
                );

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withErrors([
                    'booking' =>
                        'Une erreur est survenue lors de la création de la réservation.',
                ])
                ->withInput();
        }
    }
}