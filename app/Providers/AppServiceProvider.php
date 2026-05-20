<?php

namespace App\Providers;

use App\Events\BookingConfirmed;
use App\Listeners\ClearBookingCache;
use App\Models\Booking;
use App\Observers\BookingObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());
        Booking::observe(BookingObserver::class);

        Event::listen(
            BookingConfirmed::class,
            ClearBookingCache::class,
        );
    }
}
