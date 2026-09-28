<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use App\Models\Flight;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $airports = Airport::where('is_active', true)
            ->orderBy('city')
            ->orderBy('name')
            ->get();

        $flights = collect();

        if ($request->filled([
            'departure',
            'arrival',
            'departure_date',
        ])) {

            $validated = $request->validate([
                'departure' => 'required|exists:airports,code',
                'arrival' => 'required|exists:airports,code|different:departure',
                'departure_date' => 'required|date',
                'return_date' => 'nullable|date|after_or_equal:departure_date',
                'passengers' => 'required|integer|min:1|max:9',
            ]);

            $flights = Flight::with([
                'airline',
                'departureAirport',
                'arrivalAirport',
            ])
                ->where('is_active', true)
                ->whereHas('departureAirport', function ($query) use ($validated) {
                    $query->where('code', $validated['departure']);
                })
                ->whereHas('arrivalAirport', function ($query) use ($validated) {
                    $query->where('code', $validated['arrival']);
                })
                ->whereDate(
                    'departure_at',
                    $validated['departure_date']
                )
                ->where(
                    'available_seats',
                    '>=',
                    $validated['passengers']
                )
                ->orderBy('departure_at')
                ->get();
        }

        return view('flights.index', compact(
            'flights',
            'airports'
        ));
    }
}