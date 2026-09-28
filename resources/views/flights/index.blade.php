<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Recherche de vols - Yada Voyage</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fa;
            color: #1f2937;
        }

        header {
            background: #0f4c81;
            color: white;
            padding: 20px 8%;
        }

        header h1 {
            font-size: 28px;
        }

        header p {
            margin-top: 5px;
            opacity: 0.9;
        }

        .container {
            width: 84%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .search-box {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 35px;
        }

        .search-box h2 {
            margin-bottom: 20px;
        }

        .search-form {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 7px;
            font-weight: bold;
        }

        .form-group select,
        .form-group input {
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            width: 100%;
        }

        .search-button {
            grid-column: 1 / -1;
            background: #0f4c81;
            color: white;
            border: none;
            padding: 13px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
        }

        .search-button:hover {
            background: #0b3b63;
        }

        .title {
            margin-bottom: 25px;
        }

        .title h2 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .title p {
            color: #6b7280;
        }

        .search-info {
            background: #e0f2fe;
            color: #075985;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .flight-card {
            background: white;
            border-radius: 14px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .flight-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .airline {
            font-size: 20px;
            font-weight: bold;
        }

        .flight-number {
            color: #6b7280;
            margin-top: 5px;
        }

        .direct {
            background: #dcfce7;
            color: #166534;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .stops {
            background: #fef3c7;
            color: #92400e;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .route {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 25px;
            margin: 25px 0;
        }

        .airport {
            display: flex;
            flex-direction: column;
        }

        .airport.arrival {
            text-align: right;
        }

        .airport strong {
            font-size: 30px;
        }

        .airport .city {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        .airport .name {
            color: #6b7280;
            font-size: 13px;
            margin-top: 4px;
        }

        .airport .time {
            font-size: 18px;
            font-weight: bold;
            margin-top: 8px;
        }

        .airport .date {
            color: #6b7280;
            font-size: 13px;
            margin-top: 3px;
        }

        .flight-middle {
            min-width: 180px;
            text-align: center;
        }

        .plane {
            font-size: 25px;
            margin-bottom: 8px;
        }

        .duration {
            font-weight: bold;
            color: #374151;
        }

        .details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            gap: 20px;
        }

        .flight-details {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .detail-value {
            font-weight: bold;
        }

        .price {
            font-size: 24px;
            font-weight: bold;
            color: #0f4c81;
        }

        .seats {
            color: #16a34a;
            margin-top: 5px;
            font-size: 14px;
        }

        .btn {
            background: #0f4c81;
            color: white;
            text-decoration: none;
            padding: 12px 22px;
            border-radius: 8px;
            display: inline-block;
            white-space: nowrap;
        }

        .btn:hover {
            background: #0b3b63;
        }

        .empty {
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 12px;
        }

        .empty h3 {
            margin-bottom: 10px;
        }

        .empty p {
            color: #6b7280;
        }

        .errors {
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        @media (max-width: 900px) {
            .search-form {
                grid-template-columns: repeat(2, 1fr);
            }

            .route {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .airport,
            .airport.arrival {
                text-align: center;
            }

            .flight-middle {
                margin: 10px auto;
            }

            .details {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn {
                width: 100%;
                text-align: center;
            }
        }

        @media (max-width: 600px) {
            .container {
                width: 94%;
                margin: 20px auto;
            }

            .search-form {
                grid-template-columns: 1fr;
            }

            .flight-card {
                padding: 18px;
            }

            .flight-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .airport strong {
                font-size: 26px;
            }

            .flight-details {
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
</head>

<body>

<header>
    <h1>Yada Voyage</h1>
    <p>Réservez votre voyage en toute simplicité</p>
</header>

<div class="container">

    @if ($errors->any())
        <div class="errors">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- ========================= --}}
    {{-- RECHERCHE --}}
    {{-- ========================= --}}

    <div class="search-box">

        <h2>🔎 Rechercher un vol</h2>

        <form
            method="GET"
            action="{{ route('flights.index') }}"
            class="search-form"
        >

            <div class="form-group">
                <label for="departure">
                    Aéroport de départ
                </label>

                <select
                    name="departure"
                    id="departure"
                    required
                >
                    <option value="">
                        Choisir un aéroport
                    </option>

                    @foreach ($airports as $airport)
                        <option
                            value="{{ $airport->code }}"
                            {{ request('departure') == $airport->code ? 'selected' : '' }}
                        >
                            {{ $airport->city }} -
                            {{ $airport->code }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="arrival">
                    Aéroport d'arrivée
                </label>

                <select
                    name="arrival"
                    id="arrival"
                    required
                >
                    <option value="">
                        Choisir un aéroport
                    </option>

                    @foreach ($airports as $airport)
                        <option
                            value="{{ $airport->code }}"
                            {{ request('arrival') == $airport->code ? 'selected' : '' }}
                        >
                            {{ $airport->city }} -
                            {{ $airport->code }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="departure_date">
                    Date de départ
                </label>

                <input
                    type="date"
                    name="departure_date"
                    id="departure_date"
                    value="{{ request('departure_date') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="return_date">
                    Date de retour
                </label>

                <input
                    type="date"
                    name="return_date"
                    id="return_date"
                    value="{{ request('return_date') }}"
                >
            </div>

            <div class="form-group">
                <label for="passengers">
                    Passagers
                </label>

                <select
                    name="passengers"
                    id="passengers"
                    required
                >
                    @for ($i = 1; $i <= 9; $i++)
                        <option
                            value="{{ $i }}"
                            {{ request('passengers', 1) == $i ? 'selected' : '' }}
                        >
                            {{ $i }}
                            passager{{ $i > 1 ? 's' : '' }}
                        </option>
                    @endfor
                </select>
            </div>

            <button
                type="submit"
                class="search-button"
            >
                🔎 Rechercher des vols
            </button>

        </form>

    </div>


    {{-- ========================= --}}
    {{-- RÉSULTATS --}}
    {{-- ========================= --}}

    @if (request()->filled([
        'departure',
        'arrival',
        'departure_date'
    ]))

        <div class="title">

            <h2>
                Vols disponibles
            </h2>

            <p>
                Résultats pour
                <strong>{{ request('departure') }}</strong>
                →
                <strong>{{ request('arrival') }}</strong>
                le
                <strong>
                    {{ \Carbon\Carbon::parse(request('departure_date'))->format('d/m/Y') }}
                </strong>

                @if (request('passengers'))
                    — {{ request('passengers') }}
                    passager{{ request('passengers') > 1 ? 's' : '' }}
                @endif
            </p>

        </div>


        @if ($flights->count() > 0)

            <div class="search-info">

                ✈️

                <strong>
                    {{ $flights->count() }}
                </strong>

                vol{{ $flights->count() > 1 ? 's' : '' }}
                disponible{{ $flights->count() > 1 ? 's' : '' }}
                pour votre recherche.

            </div>

        @endif


        @forelse ($flights as $flight)

            <div class="flight-card">

                {{-- EN-TÊTE DU VOL --}}

                <div class="flight-header">

                    <div>

                        <div class="airline">
                            {{ $flight->airline->name }}
                        </div>

                        <div class="flight-number">
                            Vol {{ $flight->flight_number }}
                        </div>

                    </div>


                    @if ($flight->stops == 0)

                        <span class="direct">
                            ✓ Vol direct
                        </span>

                    @else

                        <span class="stops">
                            {{ $flight->stops }}
                            escale{{ $flight->stops > 1 ? 's' : '' }}
                        </span>

                    @endif

                </div>


                {{-- ITINÉRAIRE --}}

                <div class="route">

                    {{-- DÉPART --}}

                    <div class="airport">

                        <strong>
                            {{ $flight->departureAirport->code }}
                        </strong>

                        <span class="city">
                            {{ $flight->departureAirport->city }}
                        </span>

                        <span class="name">
                            {{ $flight->departureAirport->name }}
                        </span>

                        <span class="time">
                            {{ $flight->departure_at->format('H:i') }}
                        </span>

                        <span class="date">
                            {{ $flight->departure_at->format('d/m/Y') }}
                        </span>

                    </div>


                    {{-- DURÉE --}}

                    <div class="flight-middle">

                        <div class="plane">
                            ✈️
                        </div>

                        <div class="duration">

                            @php
                                $hours = intdiv(
                                    $flight->duration_minutes,
                                    60
                                );

                                $minutes = $flight->duration_minutes % 60;
                            @endphp

                            Durée :

                            @if ($hours > 0)
                                {{ $hours }}h
                            @endif

                            @if ($minutes > 0)
                                {{ $minutes }}min
                            @endif

                        </div>

                        <div>
                            {{ $flight->stops == 0 ? 'Direct' : $flight->stops . ' escale(s)' }}
                        </div>

                    </div>


                    {{-- ARRIVÉE --}}

                    <div class="airport arrival">

                        <strong>
                            {{ $flight->arrivalAirport->code }}
                        </strong>

                        <span class="city">
                            {{ $flight->arrivalAirport->city }}
                        </span>

                        <span class="name">
                            {{ $flight->arrivalAirport->name }}
                        </span>

                        <span class="time">
                            {{ $flight->arrival_at->format('H:i') }}
                        </span>

                        <span class="date">
                            {{ $flight->arrival_at->format('d/m/Y') }}
                        </span>

                    </div>

                </div>


                {{-- INFORMATIONS + PRIX --}}

                <div class="details">

                    <div class="flight-details">

                        <div class="detail-item">

                            <span class="detail-label">
                                Compagnie
                            </span>

                            <span class="detail-value">
                                {{ $flight->airline->name }}
                            </span>

                        </div>


                        <div class="detail-item">

                            <span class="detail-label">
                                Numéro de vol
                            </span>

                            <span class="detail-value">
                                {{ $flight->flight_number }}
                            </span>

                        </div>


                        <div class="detail-item">

                            <span class="detail-label">
                                Escales
                            </span>

                            <span class="detail-value">

                                @if ($flight->stops == 0)
                                    Direct
                                @else
                                    {{ $flight->stops }}
                                    escale{{ $flight->stops > 1 ? 's' : '' }}
                                @endif

                            </span>

                        </div>


                        <div class="detail-item">

                            <span class="detail-label">
                                Places disponibles
                            </span>

                            <span class="detail-value seats">
                                {{ $flight->available_seats }}
                            </span>

                        </div>

                    </div>


                    {{-- PRIX + BOUTON --}}

                    <div>

                        <div class="price">

                            {{ number_format(
                                $flight->price,
                                0,
                                ',',
                                ' '
                            ) }}

                            FCFA

                        </div>

                        <div class="seats">
                            Places disponibles
                        </div>

                        <br>

                        <a
                            href="{{ route('bookings.create', $flight->id) }}"
                            class="btn"
                        >
                            Choisir ce vol
                        </a>

                    </div>

                </div>

            </div>

        @empty

            <div class="empty">

                <h3>
                    Aucun vol trouvé
                </h3>

                <p>
                    Aucun vol ne correspond à votre recherche.
                </p>

            </div>

        @endforelse


    @else

        <div class="empty">

            <h3>
                Recherchez votre prochain vol ✈️
            </h3>

            <p>
                Sélectionnez votre départ, votre destination,
                votre date et le nombre de passagers.
            </p>

        </div>

    @endif

</div>

</body>
</html>