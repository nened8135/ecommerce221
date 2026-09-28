<!DOCTYPE html>

<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

```
<title>Yada Voyage - Réservation de vols</title>

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

    .header {
        background: #ffffff;
        padding: 20px 7%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .logo {
        font-size: 28px;
        font-weight: bold;
        color: #0f766e;
    }

    .nav a {
        text-decoration: none;
        color: #374151;
        margin-left: 25px;
        font-weight: 500;
    }

    .nav a:hover {
        color: #0f766e;
    }

    .hero {
        background: linear-gradient(
            rgba(0, 0, 0, 0.45),
            rgba(0, 0, 0, 0.45)
        ),
        url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1800&q=80');

        background-size: cover;
        background-position: center;

        min-height: 520px;

        display: flex;
        justify-content: center;
        align-items: center;

        padding: 60px 20px;
    }

    .hero-content {
        width: 100%;
        max-width: 1100px;
        text-align: center;
    }

    .hero h1 {
        color: white;
        font-size: 48px;
        margin-bottom: 12px;
    }

    .hero p {
        color: white;
        font-size: 20px;
        margin-bottom: 35px;
    }

    .search-box {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .search-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
        text-align: left;
    }

    .field label {
        display: block;
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 7px;
        color: #374151;
    }

    .field input,
    .field select {
        width: 100%;
        padding: 13px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 15px;
        background: white;
    }

    .field input:focus,
    .field select:focus {
        outline: none;
        border-color: #0f766e;
    }

    .search-button {
        width: 100%;
        margin-top: 20px;
        padding: 15px;
        border: none;
        border-radius: 8px;
        background: #0f766e;
        color: white;
        font-size: 17px;
        font-weight: bold;
        cursor: pointer;
    }

    .search-button:hover {
        background: #115e59;
    }

    .services {
        max-width: 1100px;
        margin: 50px auto;
        padding: 0 20px;
    }

    .services h2 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 30px;
    }

    .service-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }

    .service {
        background: white;
        padding: 30px;
        text-align: center;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    }

    .service-icon {
        font-size: 40px;
        margin-bottom: 15px;
    }

    .service h3 {
        margin-bottom: 10px;
    }

    .service p {
        color: #6b7280;
        line-height: 1.5;
    }

    footer {
        background: #111827;
        color: white;
        text-align: center;
        padding: 25px;
        margin-top: 50px;
    }

    @media (max-width: 768px) {

        .header {
            padding: 15px 20px;
        }

        .nav {
            display: none;
        }

        .hero h1 {
            font-size: 34px;
        }

        .hero p {
            font-size: 17px;
        }

        .search-grid {
            grid-template-columns: 1fr;
        }

        .service-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
```

</head>

<body>

<header class="header">

```
<div class="logo">
    Yada Voyage
</div>

<nav class="nav">
    <a href="/">Accueil</a>

    <a href="{{ route('flights.index') }}">
        Vols
    </a>

    <a href="#">
        À propos
    </a>

    <a href="#">
        Contact
    </a>
</nav>
```

</header>

<section class="hero">

```
<div class="hero-content">

    <h1>
        Voyagez avec Yada Voyage
    </h1>

    <p>
        Trouvez et réservez votre billet d'avion simplement.
    </p>


    <div class="search-box">

        <form
            method="GET"
            action="{{ route('flights.index') }}"
        >

            <div class="search-grid">


                {{-- AÉROPORT DE DÉPART --}}

                <div class="field">

                    <label for="departure">
                        Aéroport de départ
                    </label>

                    <select
                        name="departure"
                        id="departure"
                        required
                    >

                        <option value="">
                            Choisir l'aéroport de départ
                        </option>

                        @foreach ($airports as $airport)

                            <option value="{{ $airport->code }}">
                                {{ $airport->city }}
                                — {{ $airport->name }}
                                ({{ $airport->code }})
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- AÉROPORT D'ARRIVÉE --}}

                <div class="field">

                    <label for="arrival">
                        Aéroport d'arrivée
                    </label>

                    <select
                        name="arrival"
                        id="arrival"
                        required
                    >

                        <option value="">
                            Choisir l'aéroport d'arrivée
                        </option>

                        @foreach ($airports as $airport)

                            <option value="{{ $airport->code }}">
                                {{ $airport->city }}
                                — {{ $airport->name }}
                                ({{ $airport->code }})
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- DATE DE DÉPART --}}

                <div class="field">

                    <label for="departure_date">
                        Date de départ
                    </label>

                    <input
                        type="date"
                        name="departure_date"
                        id="departure_date"
                        required
                    >

                </div>


                {{-- DATE DE RETOUR --}}

                <div class="field">

                    <label for="return_date">
                        Date de retour
                    </label>

                    <input
                        type="date"
                        name="return_date"
                        id="return_date"
                    >

                </div>


                {{-- PASSAGERS --}}

                <div class="field">

                    <label for="passengers">
                        Passagers
                    </label>

                    <select
                        name="passengers"
                        id="passengers"
                        required
                    >

                        @for ($i = 1; $i <= 9; $i++)

                            <option value="{{ $i }}">
                                {{ $i }}
                                {{ $i === 1 ? 'passager' : 'passagers' }}
                            </option>

                        @endfor

                    </select>

                </div>

            </div>


            <button
                type="submit"
                class="search-button"
            >
                🔎 Rechercher des vols
            </button>

        </form>

    </div>

</div>
```

</section>

<section class="services">

```
<h2>
    Nos services
</h2>

<div class="service-grid">


    <div class="service">

        <div class="service-icon">
            ✈️
        </div>

        <h3>
            Billets d'avion
        </h3>

        <p>
            Recherchez et réservez vos vols vers différentes
            destinations.
        </p>

    </div>


    <div class="service">

        <div class="service-icon">
            💳
        </div>

        <h3>
            Paiement en ligne
        </h3>

        <p>
            Payez votre réservation en ligne de manière simple
            et sécurisée.
        </p>

    </div>


    <div class="service">

        <div class="service-icon">
            🎫
        </div>

        <h3>
            Réservation
        </h3>

        <p>
            Recevez votre confirmation et votre référence
            de réservation.
        </p>

    </div>

</div>
```

</section>

<footer>

```
© {{ date('Y') }} Yada Voyage — Tous droits réservés.
```

</footer>

</body>
</html>
