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
        $this->mergeConfigFrom(
            __DIR__.'/../config/phonable.php', 'phonable'
        );

        $this->app->bind(
            abstract: 'phone-identification',
            concrete: fn ($app) => new Identification\Manager($app),
        );

        $this->app->bind(
            abstract: 'phone-verification',
            concrete: fn ($app) => new Verification\Manager($app),
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
