<?php

namespace Roomies\Phonable;

use Illuminate\Support\ServiceProvider;

class PhonableServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            abstract: 'phone-identification',
            concrete: Identification\Manager::class
        );

        $this->app->bind(
            abstract: 'phone-verification',
            concrete: Verification\Manager::class
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/phonable.php' => config_path('phonable.php'),
        ]);
    }
}
