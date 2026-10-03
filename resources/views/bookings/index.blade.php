<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mes réservations
            </h2>

            <a
                href="{{ route('flights.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
            >
                Rechercher un vol
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-100 min-h-screen">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($bookings->isEmpty())

                <div class="bg-white rounded-xl shadow-sm p-10 text-center">

                    <div class="text-5xl mb-4">
                        ✈️
                    </div>

                    <h3 class="text-xl font-semibold text-gray-800 mb-2">
                        Aucune réservation
                    </h3>

                    <p class="text-gray-600 mb-6">
                        Vous n'avez encore effectué aucune réservation.
                    </p>

                    <a
                        href="{{ route('flights.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700"
                    >
                        Rechercher un vol
                    </a>

                </div>

            @else

                <div class="grid grid-cols-1 gap-6">

                    @foreach($bookings as $booking)

                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">

                            <div class="p-6">

                                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                                    <div>
                                        <p class="text-sm text-gray-500">
                                            Référence
                                        </p>

                                        <p class="text-lg font-bold text-gray-900">
                                            {{ $booking->booking_reference }}
                                        </p>
                                    </div>

                                    <div>

                                        @if($booking->status === 'pending')

                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                En attente de paiement
                                            </span>

                                        @elseif($booking->status === 'paid')

                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                Payée
                                            </span>

                                        @elseif($booking->status === 'cancelled')

                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                Annulée
                                            </span>

                                        @else

                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($booking->status) }}
                                            </span>

                                        @endif

                                    </div>

                                </div>

                                <div class="border-t border-gray-200 my-6"></div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                                    <div>
                                        <p class="text-sm text-gray-500">
                                            Compagnie
                                        </p>

                                        <p class="font-semibold text-gray-900">
                                            {{ $booking->flight->airline->name }}
                                        </p>

                                        <p class="text-sm text-gray-500">
                                            {{ $booking->flight->flight_number }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500">
                                            Départ
                                        </p>

                                        <p class="font-semibold text-gray-900">
                                            {{ $booking->flight->departureAirport->code }}
                                        </p>

                                        <p class="text-sm text-gray-500">
                                            {{ $booking->flight->departure_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500">
                                            Arrivée
                                        </p>

                                        <p class="font-semibold text-gray-900">
                                            {{ $booking->flight->arrivalAirport->code }}
                                        </p>

                                        <p class="text-sm text-gray-500">
                                            {{ $booking->flight->arrival_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm text-gray-500">
                                            Montant
                                        </p>

                                        <p class="font-bold text-blue-600">
                                            {{ number_format($booking->total_amount, 0, ',', ' ') }}
                                            FCFA
                                        </p>
                                    </div>

                                </div>

                                <div class="mt-6 flex flex-col sm:flex-row gap-3">

                                    <a
                                        href="{{ route('bookings.show', $booking) }}"
                                        class="inline-flex justify-center items-center px-5 py-2.5 bg-gray-800 text-white rounded-lg font-semibold hover:bg-gray-700"
                                    >
                                        Voir les détails
                                    </a>

                                    @if($booking->status === 'pending')

                                        <a
                                            href="{{ route('payments.create', $booking) }}"
                                            class="inline-flex justify-center items-center px-5 py-2.5 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700"
                                        >
                                            Payer maintenant
                                        </a>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>

    </div>

</x-app-layout>