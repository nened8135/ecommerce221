<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Informations passager - Yada Voyage</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 25px;
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 600;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 35px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 30px;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 30px;
        }

        .flight-info {
            background: #eff6ff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .flight-info h2 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #1d4ed8;
        }

        .flight-info p {
            margin: 8px 0;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin: 30px 0 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        input,
        select {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 15px;
            background: white;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .baggage-section {
            margin-top: 10px;
        }

        .baggage-option {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .baggage-info {
            flex: 1;
        }

        .baggage-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .baggage-description {
            color: #64748b;
            font-size: 14px;
        }

        .baggage-price {
            margin-top: 5px;
            color: #1d4ed8;
            font-weight: 600;
        }

        .baggage-select {
            width: 100px;
        }

        .summary {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .summary-total {
            border-top: 1px solid #d1d5db;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 20px;
            font-weight: bold;
            color: #1d4ed8;
        }

        .submit-button {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 17px;
            margin-top: 25px;
            background: #1d4ed8;
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-button:hover {
            background: #1e40af;
        }

        .error-message {
            background: #fef2f2;
            color: #b91c1c;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .error-message ul {
            margin-bottom: 0;
        }

        .included {
            color: #047857;
            font-weight: 600;
        }

        @media (max-width: 700px) {
            .container {
                margin: 20px auto;
                padding: 15px;
            }

            .card {
                padding: 22px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-group.full {
                grid-column: auto;
            }

            .baggage-option {
                flex-direction: column;
                align-items: stretch;
            }

            .baggage-select {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <a href="{{ route('flights.index') }}" class="back-button">
        ← Retour aux vols
    </a>

    <div class="card">

        <h1>Informations du passager</h1>

        <p class="subtitle">
            Veuillez renseigner les informations nécessaires pour votre réservation.
        </p>

        {{-- Messages d'erreur --}}
        @if($errors->any())
            <div class="error-message">
                <strong>Veuillez corriger les erreurs suivantes :</strong>

                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Informations du vol --}}
        <div class="flight-info">

            <h2>
                {{ $flight->airline->name }}
                — {{ $flight->flight_number }}
            </h2>

            <p>
                <strong>Départ :</strong>
                {{ $flight->departureAirport->city }}
                ({{ $flight->departureAirport->code }})
            </p>

            <p>
                <strong>Arrivée :</strong>
                {{ $flight->arrivalAirport->city }}
                ({{ $flight->arrivalAirport->code }})
            </p>

            <p>
                <strong>Date :</strong>
                {{ $flight->departure_at->format('d/m/Y') }}
            </p>

            <p>
                <strong>Heure de départ :</strong>
                {{ $flight->departure_at->format('H:i') }}
            </p>

            <p>
                <strong>Heure d'arrivée :</strong>
                {{ $flight->arrival_at->format('H:i') }}
            </p>

            @if($flight->duration_minutes)
                @php
                    $hours = intdiv($flight->duration_minutes, 60);
                    $minutes = $flight->duration_minutes % 60;
                @endphp

                <p>
                    <strong>Durée :</strong>

                    @if($hours > 0)
                        {{ $hours }} h
                    @endif

                    @if($minutes > 0)
                        {{ $minutes }} min
                    @endif
                </p>
            @endif

            <p>
                <strong>Escales :</strong>

                @if($flight->stops == 0)
                    Direct
                @elseif($flight->stops == 1)
                    1 escale
                @else
                    {{ $flight->stops }} escales
                @endif
            </p>

            <p>
                <strong>Prix du billet :</strong>
                {{ number_format($flight->price, 0, ',', ' ') }} FCFA
            </p>

        </div>


        <form
            method="POST"
            action="{{ route('bookings.store', $flight) }}"
        >

            @csrf


            {{-- ========================= --}}
            {{-- INFORMATIONS DU PASSAGER --}}
            {{-- ========================= --}}

            <div class="section-title">
                Informations personnelles
            </div>

            <div class="form-grid">

                {{-- Prénom --}}
                <div class="form-group">

                    <label for="first_name">
                        Prénom *
                    </label>

                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        placeholder="Ex : Awa"
                        required
                    >

                </div>


                {{-- Nom --}}
                <div class="form-group">

                    <label for="last_name">
                        Nom *
                    </label>

                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        placeholder="Ex : Diop"
                        required
                    >

                </div>


                {{-- Date de naissance --}}
                <div class="form-group">

                    <label for="date_of_birth">
                        Date de naissance *
                    </label>

                    <input
                        type="date"
                        id="date_of_birth"
                        name="date_of_birth"
                        value="{{ old('date_of_birth') }}"
                        required
                    >

                </div>


                {{-- Nationalité --}}
                <div class="form-group">

                    <label for="nationality">
                        Nationalité *
                    </label>

                    <input
                        type="text"
                        id="nationality"
                        name="nationality"
                        value="{{ old('nationality') }}"
                        placeholder="Ex : Sénégalaise"
                        required
                    >

                </div>


                {{-- Téléphone --}}
                <div class="form-group">

                    <label for="phone">
                        Numéro de téléphone *
                    </label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone') }}"
                        placeholder="Ex : 77 123 45 67"
                        required
                    >

                </div>


                {{-- Passeport --}}
                <div class="form-group">

                    <label for="passport_number">
                        Numéro de passeport
                    </label>

                    <input
                        type="text"
                        id="passport_number"
                        name="passport_number"
                        value="{{ old('passport_number') }}"
                        placeholder="Ex : A12345678"
                    >

                </div>


                {{-- Expiration passeport --}}
                <div class="form-group">

                    <label for="passport_expiry">
                        Date d'expiration du passeport
                    </label>

                    <input
                        type="date"
                        id="passport_expiry"
                        name="passport_expiry"
                        value="{{ old('passport_expiry') }}"
                    >

                </div>

            </div>


            {{-- ========================= --}}
            {{-- BAGAGES --}}
            {{-- ========================= --}}

            <div class="section-title">
                Bagages et options
            </div>

            <div class="baggage-section">

                @forelse($baggageOptions as $option)

                    <div class="baggage-option">

                        <div class="baggage-info">

                            <div class="baggage-name">
                                {{ $option->name }}
                            </div>

                            <div class="baggage-description">
                                Poids : {{ $option->weight_kg }} kg
                            </div>

                            <div class="baggage-price">

                                @if($option->is_included)
                                    <span class="included">
                                        Inclus dans le billet
                                    </span>
                                @else
                                    {{ number_format($option->price, 0, ',', ' ') }}
                                    FCFA / bagage
                                @endif

                            </div>

                        </div>


                        <div>

                            <label for="baggage_{{ $option->id }}">
                                Quantité
                            </label>

                            <select
                                class="baggage-select"
                                id="baggage_{{ $option->id }}"
                                name="baggage[{{ $option->id }}]"
                            >

                                @for($i = 0; $i <= 5; $i++)

                                    <option
                                        value="{{ $i }}"
                                        {{ old("baggage.{$option->id}", 0) == $i ? 'selected' : '' }}
                                    >
                                        {{ $i }}
                                    </option>

                                @endfor

                            </select>

                        </div>

                    </div>

                @empty

                    <p>
                        Aucune option de bagage disponible.
                    </p>

                @endforelse

            </div>


            {{-- ========================= --}}
            {{-- RÉCAPITULATIF --}}
            {{-- ========================= --}}

            <div class="summary">

                <div class="summary-row">

                    <span>
                        Prix du billet
                    </span>

                    <strong>
                        {{ number_format($flight->price, 0, ',', ' ') }} FCFA
                    </strong>

                </div>


                <div class="summary-row">

                    <span>
                        Bagages supplémentaires
                    </span>

                    <strong id="baggage-total">
                        0 FCFA
                    </strong>

                </div>


                <div class="summary-row summary-total">

                    <span>
                        Total
                    </span>

                    <strong id="grand-total">
                        {{ number_format($flight->price, 0, ',', ' ') }} FCFA
                    </strong>

                </div>

            </div>


            {{-- Bouton --}}
            <button
                type="submit"
                class="submit-button"
            >
                Continuer vers le paiement
            </button>

        </form>

    </div>

</div>


{{-- ========================= --}}
{{-- CALCUL DU TOTAL --}}
{{-- ========================= --}}

<script>

    const ticketPrice = {{ (float) $flight->price }};

    const baggageOptions = @json(
        $baggageOptions->mapWithKeys(function ($option) {
            return [
                $option->id => [
                    'price' => $option->is_included
                        ? 0
                        : (float) $option->price
                ]
            ];
        })
    );


    function updateTotal() {

        let baggageTotal = 0;


        Object.keys(baggageOptions).forEach(function (id) {

            const select = document.getElementById(
                'baggage_' + id
            );

            if (!select) {
                return;
            }


            const quantity = parseInt(
                select.value
            ) || 0;


            const price =
                baggageOptions[id].price;


            baggageTotal +=
                quantity * price;

        });


        const grandTotal =
            ticketPrice + baggageTotal;


        document.getElementById(
            'baggage-total'
        ).textContent =
            new Intl.NumberFormat('fr-FR').format(
                baggageTotal
            ) + ' FCFA';


        document.getElementById(
            'grand-total'
        ).textContent =
            new Intl.NumberFormat('fr-FR').format(
                grandTotal
            ) + ' FCFA';

    }


    document
        .querySelectorAll('.baggage-select')
        .forEach(function (select) {

            select.addEventListener(
                'change',
                updateTotal
            );

        });


    updateTotal();

</script>

</body>
</html>