<?php 
namespace Database\Seeders; 
use App\Models\Airport; 
use App\Models\Airline; 
use App\Models\Flight; 
use Illuminate\Database\Seeder; 
class InternationalFlightSeeder extends Seeder 
{ 
    public function run(): void

    {
        /*
        |--------------------------------------------------------------------------
        | AÉROPORTS
        |--------------------------------------------------------------------------
        */

        $airports = Airport::whereIn('code', [
            'DSS',
            'CDG',
            'ABJ',
            'CMN',
            'IST',
            'MAD',
            'LHR',
            'DXB',
        ])->get()->keyBy('code');

        /*
        |--------------------------------------------------------------------------
        | COMPAGNIES
        |--------------------------------------------------------------------------
        */

        $airlines = Airline::whereIn('code', [
            'HC',
            'AF',
            'AT',
            'TK',
            'EK',
            'ET',
        ])->get()->keyBy('code');

        /*
        |--------------------------------------------------------------------------
        | DURÉES DES TRAJETS
        |--------------------------------------------------------------------------
        */

        $durations = [
            'DSS-CDG' => 390,
            'DSS-ABJ' => 180,
            'DSS-CMN' => 330,
            'DSS-IST' => 450,
            'DSS-MAD' => 330,
            'DSS-LHR' => 510,
            'DSS-DXB' => 630,

            'CDG-ABJ' => 390,
            'CDG-CMN' => 210,
            'CDG-IST' => 210,
            'CDG-MAD' => 130,
            'CDG-LHR' => 80,
            'CDG-DXB' => 420,

            'ABJ-CMN' => 240,
            'ABJ-IST' => 450,
            'ABJ-MAD' => 360,
            'ABJ-LHR' => 420,
            'ABJ-DXB' => 570,

            'CMN-IST' => 300,
            'CMN-MAD' => 120,
            'CMN-LHR' => 210,
            'CMN-DXB' => 630,

            'IST-MAD' => 250,
            'IST-LHR' => 250,
            'IST-DXB' => 300,

            'MAD-LHR' => 150,
            'MAD-DXB' => 420,

            'LHR-DXB' => 420,
        ];

        /*
        |--------------------------------------------------------------------------
        | PRIX DE BASE
        |--------------------------------------------------------------------------
        */

        $prices = [
            'DSS-CDG' => 250000,
            'DSS-ABJ' => 150000,
            'DSS-CMN' => 190000,
            'DSS-IST' => 325000,
            'DSS-MAD' => 275000,
            'DSS-LHR' => 440000,
            'DSS-DXB' => 400000,

            'CDG-ABJ' => 300000,
            'CDG-CMN' => 180000,
            'CDG-IST' => 220000,
            'CDG-MAD' => 180000,
            'CDG-LHR' => 120000,
            'CDG-DXB' => 450000,

            'ABJ-CMN' => 220000,
            'ABJ-IST' => 380000,
            'ABJ-MAD' => 350000,
            'ABJ-LHR' => 420000,
            'ABJ-DXB' => 500000,

            'CMN-IST' => 250000,
            'CMN-MAD' => 160000,
            'CMN-LHR' => 280000,
            'CMN-DXB' => 380000,

            'IST-MAD' => 230000,
            'IST-LHR' => 260000,
            'IST-DXB' => 300000,

            'MAD-LHR' => 180000,
            'MAD-DXB' => 420000,

            'LHR-DXB' => 450000,
        ];

        /*
        |--------------------------------------------------------------------------
        | COMPAGNIES
        |--------------------------------------------------------------------------
        */

        $airlineCodes = [
            'HC',
            'AF',
            'AT',
            'TK',
            'EK',
            'ET',
        ];

        /*
        |--------------------------------------------------------------------------
        | PÉRIODE DE DÉMONSTRATION
        |--------------------------------------------------------------------------
        |
        | 12 mois de vols :
        | 1er octobre 2026 → 30 septembre 2027
        |
        */

        $startDate = new \DateTime('2026-10-01');
        $endDate = new \DateTime('2027-09-30');

        /*
        |--------------------------------------------------------------------------
        | GÉNÉRATION
        |--------------------------------------------------------------------------
        */

        $airportCodes = $airports->keys()->values()->all();

        $counter = 1000;

        while ($startDate <= $endDate) {

            $date = $startDate->format('Y-m-d');

            foreach ($airportCodes as $departureCode) {

                foreach ($airportCodes as $arrivalCode) {

                    /*
                    | Même aéroport = pas de vol
                    */

                    if ($departureCode === $arrivalCode) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ROUTE
                    |--------------------------------------------------------------------------
                    */

                    $routeKey = $departureCode . '-' . $arrivalCode;

                    $reverseRouteKey = $arrivalCode . '-' . $departureCode;

                    /*
                    |--------------------------------------------------------------------------
                    | DURÉE
                    |--------------------------------------------------------------------------
                    */

                    $duration = $durations[$routeKey]
                        ?? $durations[$reverseRouteKey]
                        ?? 300;

                    /*
                    |--------------------------------------------------------------------------
                    | PRIX
                    |--------------------------------------------------------------------------
                    */

                    $price = $prices[$routeKey]
                        ?? $prices[$reverseRouteKey]
                        ?? 250000;

                    /*
                    |--------------------------------------------------------------------------
                    | COMPAGNIE
                    |--------------------------------------------------------------------------
                    */

                    $airlineCode = $airlineCodes[
                        $counter % count($airlineCodes)
                    ];

                    $airline = $airlines->get($airlineCode);

                    if (!$airline) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | HEURE DE DÉPART
                    |--------------------------------------------------------------------------
                    */

                    $departureHour = 6 + ($counter % 14);

                    $departureAt = new \DateTime(
                        $date . ' ' .
                        str_pad($departureHour, 2, '0', STR_PAD_LEFT) .
                        ':00:00'
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | HEURE D'ARRIVÉE
                    |--------------------------------------------------------------------------
                    */

                    $arrivalAt = clone $departureAt;

                    $arrivalAt->modify("+{$duration} minutes");

                    /*
                    |--------------------------------------------------------------------------
                    | NUMÉRO DE VOL
                    |--------------------------------------------------------------------------
                    */

                    $flightNumber =
                        $airlineCode .
                        str_pad($counter, 5, '0', STR_PAD_LEFT);

                    /*
                    |--------------------------------------------------------------------------
                    | CRÉATION / MISE À JOUR
                    |--------------------------------------------------------------------------
                    */

                    Flight::updateOrCreate(
                        [
                            'flight_number' => $flightNumber,
                        ],
                        [
                            'airline_id' => $airline->id,

                            'departure_airport_id' =>
                                $airports[$departureCode]->id,

                            'arrival_airport_id' =>
                                $airports[$arrivalCode]->id,

                            'flight_number' => $flightNumber,

                            'departure_at' =>
                                $departureAt->format('Y-m-d H:i:s'),

                            'arrival_at' =>
                                $arrivalAt->format('Y-m-d H:i:s'),

                            'duration_minutes' => $duration,

                            'stops' => 0,

                            'price' => $price,

                            'available_seats' =>
                                120 + ($counter % 61),

                            'is_active' => true,
                        ]
                    );

                    $counter++;
                }
            }

            /*
            | Jour suivant
            */

            $startDate->modify('+1 day');
        }
    }
}

