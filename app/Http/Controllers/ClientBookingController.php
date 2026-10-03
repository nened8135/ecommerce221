<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class ClientBookingController extends Controller
{
    /**
     * Afficher les réservations du client connecté.
     */
    public function index()
    {
        $bookings = Booking::with([
            'flight.airline',
            'flight.departureAirport',
            'flight.arrivalAirport',
            'passengers',
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Afficher le détail d'une réservation.
     */
    public function show(Booking $booking)
    {
        // Sécurité :
        // le client ne peut voir que ses propres réservations.
        abort_unless(
            $booking->user_id === Auth::id(),
            403
        );

        $booking->load([
            'flight.airline',
            'flight.departureAirport',
            'flight.arrivalAirport',
            'passengers',
            'bookingBaggages.baggageOption',
            'payments',
        ]);

        return view('bookings.show', compact('booking'));
    }
}