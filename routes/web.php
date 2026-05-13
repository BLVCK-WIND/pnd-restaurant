<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\GuestController;
use App\Http\Controllers\Admin\MenuItemController;
use App\Http\Controllers\Admin\OptionGroupController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Guest\BookingController;
use App\Http\Controllers\Guest\GuestController as GuestGuestController;
use App\Http\Controllers\Guest\ReviewController;
use App\Http\Controllers\Manage\BookingController as ManageBookingController;
use App\Http\Controllers\Manage\OrderController;
use App\Http\Controllers\Shared\BookingController as SharedBookingController;
use App\Http\Controllers\Staff\ScheduleController as StaffScheduleController;
use Illuminate\Support\Facades\Route;


// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'loginIndex'])->name('login.index');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/register', [RegisterController::class, 'registerIndex'])->name('register.index');
    Route::post('/register', [RegisterController::class, 'register'])->name('register');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Admin
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
    Route::post('/categories/reorder', [CategoryController::class, 'reorder'])
    ->name('categories.reorder');
    Route::resource('/categories', CategoryController::class);
    Route::resource('/menuitems', MenuItemController::class);
    Route::resource('/areas', AreaController::class);
    Route::resource('/tables', TableController::class);
    // Staff
    Route::resource('/staffs', StaffController::class)->except(['show']);
    Route::patch('/staffs/{staff}/toggle', [StaffController::class, 'toggle'])
         ->name('staffs.toggle');

    // Guest
    Route::get('/guests', [GuestController::class, 'index'])->name('guests.index');
    Route::get('/guests/{guest}', [GuestController::class, 'show'])->name('guests.show');
    Route::patch('/guests/{guest}/toggle', [GuestController::class, 'toggleActive'])
         ->name('guests.toggle');
    Route::delete('/guests/{guest}', [GuestController::class, 'destroy'])
         ->name('guests.destroy');

    //Schedule
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules/toggle', [ScheduleController::class, 'toggle'])->name('schedules.toggle');

    //Review
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    //OptionGroup
    Route::resource('/optiongroups', OptionGroupController::class);
});

// Staff
Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/dashboard', fn() => view('staff.dashboard'))->name('dashboard');
    Route::get('/schedules', [StaffScheduleController::class, 'index'])->name('schedules.index');
});

//Manager: admin và staff
Route::prefix('manage')
    ->name('manage.')
    ->middleware(['auth', 'role:admin,staff'])
    ->group(function () {
        Route::get('/bookings', [ManageBookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/create', [ManageBookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [ManageBookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{booking}', [ManageBookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/confirm', [ManageBookingController::class, 'confirm'])->name('bookings.confirm');
        Route::post('/bookings/{booking}/complete', [ManageBookingController::class, 'complete'])->name('bookings.complete');
        Route::post('/bookings/{booking}/cancel', [ManageBookingController::class, 'cancel'])->name('bookings.cancel');
        // Order
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}', [OrderController::class, 'cancel'])->name('orders.cancel');

        // AJAX — thêm/sửa/xoá món
        Route::post('/orders/{order}/items', [OrderController::class, 'addItem'])->name('orders.items.add');
        Route::patch('/orders/{order}/items/{item}', [OrderController::class, 'updateItem'])->name('orders.items.update');
        Route::delete('/orders/{order}/items/{item}', [OrderController::class, 'removeItem'])->name('orders.items.remove');

        // Thanh toán
        Route::post('/orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
    });
// Guest
Route::prefix('guest')->name('guest.')->middleware(['auth', 'role:guest'])->group(function () {

    Route::resource('/bookings', BookingController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::post('/reviews', [ReviewController::class, 'store'])
    ->name('reviews.store');
});

// Public
Route::get('/', [GuestGuestController::class, 'index'])->name('guest.index');
Route::get('/guest/menu',  [GuestGuestController::class, 'menu'])->name('guest.menu');
Route::get('/guest/about', [GuestGuestController::class, 'about'])->name('guest.about');
Route::get('/guest/contact', [GuestGuestController::class, 'contact'])->name('guest.contact');
Route::post('/guest/contact', [GuestGuestController::class, 'sendContact'])->name('guest.contact.send');