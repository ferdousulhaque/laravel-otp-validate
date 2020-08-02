<?php


namespace Ferdous\OtpValidator;

use Illuminate\Support\ServiceProvider;

class OtpValidatorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/otp.php' => config_path('otp.php'),
            __DIR__ . '/../template-otp' => base_path('resources/views/vendor/template-otp')
        ]);
        $this->migrateTables();
        //$this->migrateViews();
    }

    public function register()
    {
        $this->app->singleton(OtpValidator::class, function () {
            return new OtpValidator();
        });
    }

    private function migrateTables()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    private function migrateViews()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views','otp');
    }
}
