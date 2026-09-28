<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Paiement - Yada Voyage</title>

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
            max-width: 850px;
            margin: 50px auto;
            padding: 20px;
        }

        .back-button {
            display: inline-block;
            margin-bottom: 25px;
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 600;
        }

        .payment-card {
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

        .success-message {
            background: #ecfdf5;
            color: #047857;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .booking-info {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }

        .booking-info p {
            margin: 8px 0;
        }

        .amount-box {
            background: #eff6ff;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin: 25px 0;
        }

        .amount-label {
            font-size: 15px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .amount {
            font-size: 32px;
            font-weight: bold;
            color: #1d4ed8;
        }

        .payment-title {
            font-size: 20px;
            font-weight: bold;
            margin: 30px 0 15px;
        }

        .payment-method {
            display: block;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: 0.2s;
        }

        .payment-method:hover {
            border-color: #2563eb;
            background: #f8fafc;
        }

        .payment-method input {
            margin-right: 10px;
        }

        .method-name {
            font-weight: bold;
            font-size: 17px;
        }

        .method-description {
            display: block;
            margin-left: 27px;
            margin-top: 5px;
            color: #64748b;
            font-size: 14px;
        }

        .pay-button {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 17px;
            margin-top: 20px;
            background: #1d4ed8;
            color: white;
            font-size: 17px;
            font-weight: bold;
            cursor: pointer;
        }

        .pay-button:hover {
            background: #1e40af;
        }

        .security {
            text-align: center;
            color: #64748b;
            font-size: 13px;
            margin-top: 20px;
        }

        .error-message {
            background: #fef2f2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>

<div class="container">

    <a
        href="{{ route('bookings.create', $booking->flight) }}"
        class="back-button"
    >
        ← Retour aux informations du passager
    </a>

    <div class="payment-card">

        <h1>Paiement de votre réservation</h1>

        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

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

        <div class="booking-info">

            <p>
                <strong>Réservation :</strong>
                {{ $booking->booking_reference }}
            </p>

            <p>
                <strong>Vol :</strong>
                {{ $booking->flight->flight_number }}
            </p>

            <p>
                <strong>Compagnie :</strong>
                {{ $booking->flight->airline->name }}
            </p>

            <p>
                <strong>Trajet :</strong>
                {{ $booking->flight->departureAirport->city }}
                →
                {{ $booking->flight->arrivalAirport->city }}
            </p>

            <p>
                <strong>Nombre de passagers :</strong>
                {{ $booking->number_of_passengers }}
            </p>

        </div>

        <div class="amount-box">

            <div class="amount-label">
                Montant total à payer
            </div>

            <div class="amount">
                {{ number_format($booking->total_amount, 0, ',', ' ') }}
                FCFA
            </div>

        </div>

        <div class="payment-title">
            Choisissez votre moyen de paiement
        </div>

        <form
            method="POST"
            action="{{ route('payments.store', $booking) }}"
        >

            @csrf

            <!-- WAVE -->
            <label class="payment-method">

                <input
                    type="radio"
                    name="payment_method"
                    value="wave"
                    required
                >

                <span class="method-name">
                    🌊 Wave
                </span>

                <span class="method-description">
                    Paiement mobile avec Wave Sénégal
                </span>

            </label>

            <!-- ORANGE MONEY -->
            <label class="payment-method">

                <input
                    type="radio"
                    name="payment_method"
                    value="orange_money"
                >

                <span class="method-name">
                    🟠 Orange Money
                </span>

                <span class="method-description">
                    Paiement mobile avec Orange Money Sénégal
                </span>

            </label>

            <!-- WIZALL -->
            <label class="payment-method">

                <input
                    type="radio"
                    name="payment_method"
                    value="wizall"
                >

                <span class="method-name">
                    💜 Wizall
                </span>

                <span class="method-description">
                    Paiement mobile avec Wizall Sénégal
                </span>

            </label>

            <!-- CARTE BANCAIRE -->
            <label class="payment-method">

                <input
                    type="radio"
                    name="payment_method"
                    value="card"
                >

                <span class="method-name">
                    💳 Carte bancaire
                </span>

                <span class="method-description">
                    Visa, Mastercard et autres cartes compatibles
                </span>

            </label>

            <button
                type="submit"
                class="pay-button"
            >
                Payer
                {{ number_format($booking->total_amount, 0, ',', ' ') }}
                FCFA
            </button>

        </form>

        <div class="security">
            🔒 Paiement sécurisé — Yada Voyage
        </div>

    </div>

</div>

</body>
</html>