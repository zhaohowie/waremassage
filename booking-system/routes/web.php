<?php
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\StaffWorkingHourController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::resource('services', ServiceController::class);
    Route::resource('staff', StaffController::class);
    Route::resource('service-categories', ServiceCategoryController::class);
    Route::resource('appointments', AppointmentController::class);
    Route::resource('customers', CustomerController::class);
    Route::get('staff/{staff}/working-hours', [StaffWorkingHourController::class, 'edit'])
    ->name('staff.working-hours.edit');
    Route::put('staff/{staff}/working-hours', [StaffWorkingHourController::class, 'update'])
    ->name('staff.working-hours.update');
    Route::get('/booking', [AppointmentController::class, 'bookingForm'])
        ->name('booking.form');

    Route::post('/booking', [AppointmentController::class, 'storeBooking'])
        ->name('booking.store');
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])
        ->name('appointments.update-status');
    Route::get('/calendar', [AppointmentController::class, 'calendar'])
        ->name('appointments.calendar');   
    Route::patch('/appointments/{appointment}/move', [AppointmentController::class, 'move'])
    ->name('appointments.move');         
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/booking/available-times', [AppointmentController::class, 'availableTimes'])
    ->name('booking.available-times');

Route::get('/booking/staff-by-service', [AppointmentController::class, 'staffByService'])
->name('booking.staff-by-service');

Route::get('/booking/confirmation/{appointment}', [AppointmentController::class, 'bookingConfirmation'])
    ->name('booking.confirmation');

require __DIR__.'/auth.php';
