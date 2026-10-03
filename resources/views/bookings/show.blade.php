<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Détails de la réservation
            </h2>

            <a
                href="{{ route('bookings.index') }}"
                class="text-sm text-blue-600 hover:text-blue-800"
            >
                ← Mes réservations
            </a>

        </div>
    </x-slot>

    <div class="py-10 bg-gray-100 min-h-screen">

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">

                <!-- EN-TÊTE -->

                <div class="bg-gray-900 text-white p-6">

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                        <div>

                            <p class="text-sm text-gray-300">
                                Référence de réservation
                            </p>

                            <h1 class="text-2xl font-bold">
                                {{ $booking->booking_reference }}
                            </h1>

                        </div>

                        <div>

                            @if($booking->status === 'pending')

                                <span class="inline-flex px-4 py-2 rounded-full bg-yellow-500 text-white font-semibold">
                                    En attente de paiement
                                </span>

                            @elseif($booking->status === 'paid')

                                <span class="inline-flex px-4 py-2 rounded-full bg-green-600 text-white font-semibold">
                                    Réservation payée
                                </span>

                            @elseif($booking->status === 'cancelled')

                                <span class="inline-flex px-4 py-2 rounded-full bg-red-600 text-white font-semibold">
                                    Réservation annulée
                                </span>

                            @else

                                <span class="inline-flex px-4 py-2 rounded-full bg-gray-600 text-white font-semibold">
                                    {{ ucfirst($booking->status) }}
                                </span>

                            @endif

                        </div>

                    </div>

                </div>


                <div class="p-6">

                    <!-- INFORMATIONS DU VOL -->

                    <div class="mb-8">

                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            ✈️ Informations du vol
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="border rounded-lg p-5">

                                <p class="text-sm text-gray-500">
                                    Compagnie aérienne
                                </p>

                                <p class="text-lg font-bold text-gray-900">
                                    {{ $booking->flight->airline->name }}
                                </p>

                                <p class="text-sm text-gray-500">
                                    Vol {{ $booking->flight->flight_number }}
                                </p>

                            </div>


                            <div class="border rounded-lg p-5">

                                <p class="text-sm text-gray-500">
                                    Itinéraire
                                </p>

                                <p class="text-lg font-bold text-gray-900">
                                    {{ $booking->flight->departureAirport->code }}
                                    →
                                    {{ $booking->flight->arrivalAirport->code }}
                                </p>

                            </div>


                            <div class="border rounded-lg p-5">

                                <p class="text-sm text-gray-500">
                                    Départ
                                </p>

                                <p class="font-semibold text-gray-900">
                                    {{ $booking->flight->departureAirport->city }}
                                </p>

                                <p class="text-gray-600">
                                    {{ $booking->flight->departure_at->format('d/m/Y à H:i') }}
                                </p>

                            </div>


                            <div class="border rounded-lg p-5">

                                <p class="text-sm text-gray-500">
                                    Arrivée
                                </p>

                                <p class="font-semibold text-gray-900">
                                    {{ $booking->flight->arrivalAirport->city }}
                                </p>

                                <p class="text-gray-600">
                                    {{ $booking->flight->arrival_at->format('d/m/Y à H:i') }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- PASSAGER -->

                    <div class="mb-8">

                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            👤 Passager
                        </h3>

                        @foreach($booking->passengers as $passenger)

                            <div class="border rounded-lg p-5">

                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $passenger->first_name }}
                                    {{ $passenger->last_name }}
                                </p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 text-sm">

                                    <div>
                                        <span class="text-gray-500">
                                            Nationalité :
                                        </span>

                                        {{ $passenger->nationality ?? 'Non renseignée' }}
                                    </div>

                                    <div>
                                        <span class="text-gray-500">
                                            Téléphone :
                                        </span>

                                        {{ $passenger->phone }}
                                    </div>

                                    <div>
                                        <span class="text-gray-500">
                                            Passeport :
                                        </span>

                                        {{ $passenger->passport_number ?? 'Non renseigné' }}
                                    </div>

                                    <div>
                                        <span class="text-gray-500">
                                            Date de naissance :
                                        </span>

                                        {{ $passenger->date_of_birth?->format('d/m/Y') ?? 'Non renseignée' }}
                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>


                    <!-- BAGAGES -->

                    <div class="mb-8">

                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            🧳 Bagages
                        </h3>

                        @if($booking->bookingBaggages->isEmpty())

                            <p class="text-gray-500">
                                Aucun bagage supplémentaire.
                            </p>

                        @else

                            <div class="space-y-3">

                                @foreach($booking->bookingBaggages as $bookingBaggage)

                                    <div class="flex justify-between border rounded-lg p-4">

                                        <div>

                                            <p class="font-semibold">
                                                {{ $bookingBaggage->baggageOption->name }}
                                            </p>

                                            <p class="text-sm text-gray-500">
                                                Quantité :
                                                {{ $bookingBaggage->quantity }}
                                            </p>

                                        </div>

                                        <p class="font-semibold">

                                            {{ number_format(
                                                $bookingBaggage->unit_price * $bookingBaggage->quantity,
                                                0,
                                                ',',
                                                ' '
                                            ) }}

                                            FCFA

                                        </p>

                                    </div>

                                @endforeach

                            </div>

                        @endif

                    </div>


                    <!-- TOTAL -->

                    <div class="border-t pt-6">

                        <div class="flex items-center justify-between">

                            <span class="text-xl font-bold text-gray-900">
                                Total
                            </span>

                            <span class="text-2xl font-bold text-blue-600">

                                {{ number_format(
                                    $booking->total_amount,
                                    0,
                                    ',',
                                    ' '
                                ) }}

                                FCFA

                            </span>

                        </div>

                    </div>


                    <!-- PAIEMENT -->

                    @if($booking->status === 'pending')

                        <div class="mt-8">

                            <a
                                href="{{ route('payments.create', $booking) }}"
                                class="w-full inline-flex justify-center items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700"
                            >
                                Procéder au paiement
                            </a>

                        </div>

                    @elseif($booking->status === 'paid')

                        <div class="mt-8 bg-green-50 border border-green-200 rounded-lg p-5">

                            <p class="font-semibold text-green-800">
                                ✓ Votre réservation est payée.
                            </p>

                            <p class="text-sm text-green-700 mt-1">
                                Votre billet électronique pourra être consulté ici lorsqu'il sera généré.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</x-app-layout>