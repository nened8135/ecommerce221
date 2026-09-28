<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientBookingController;
use App\Models\Airport;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Accueil
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $airports = Airport::where('is_active', true)
        ->orderBy('city')
        ->orderBy('name')
        ->get();

    return view('welcome', compact('airports'));
})->name('home');

/*
|--------------------------------------------------------------------------
| Vols
|--------------------------------------------------------------------------
*/

Route::get('/flights', [FlightController::class, 'index'])
    ->name('flights.index');

/*
|--------------------------------------------------------------------------
| Catégories
|--------------------------------------------------------------------------
*/

Route::resource('categories', CategoryController::class);

/*
|--------------------------------------------------------------------------
| Tableau de bord
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profil utilisateur
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->name('profile.destroy');
    Route::get('/mes-reservations', [ClientBookingController::class, 'index'])
    ->name('bookings.index');

    Route::get('/mes-reservations/{booking}', [ClientBookingController::class, 'show'])
    ->name('bookings.show');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
        Route::get('/flights/{flight}/booking', [BookingController::class, 'create'])
        ->name('bookings.create');

    Route::post('/flights/{flight}/booking', [BookingController::class, 'store'])
        ->name('bookings.store');

    Route::get('/bookings/{booking}/payment', [PaymentController::class, 'create'])
        ->name('payments.create');

    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'store'])
        ->name('payments.store');
});

/*
|--------------------------------------------------------------------------
| Réservations et paiements
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/flights/{flight}/booking', [BookingController::class, 'create'])
        ->name('bookings.create');

    Route::post('/flights/{flight}/booking', [BookingController::class, 'store'])
        ->name('bookings.store');

    Route::get('/bookings/{booking}/payment', [PaymentController::class, 'create'])
        ->name('payments.create');

    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'store'])
        ->name('payments.store');
});

/*
|--------------------------------------------------------------------------
| Retour / annulation PayDunya
|--------------------------------------------------------------------------
*/

Route::get('/payment/return', [PaymentController::class, 'return'])
    ->name('payments.return');

Route::get('/payment/cancel', [PaymentController::class, 'cancel'])
    ->name('payments.cancel');

/*
|--------------------------------------------------------------------------
| Callback PayDunya
|--------------------------------------------------------------------------
*/

Route::post('/payment/callback', [PaymentController::class, 'callback'])
    ->name('payments.callback');

/*
|--------------------------------------------------------------------------
| Authentification Breeze
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';