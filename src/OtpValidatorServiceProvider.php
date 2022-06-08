<?php


namespace Ferdous\OtpValidator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Laravel\Lumen\Application as LumenApplication;

class OtpValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(__DIR__ . '/../config/otp.php');
        $templates = realpath( __DIR__ . '/../template-otp');
        $migrations = realpath( __DIR__ . '/database/migrations');

        // Check if the application is a Laravel OR Lumen instance to properly merge the configuration file.
        // Laravel Package Configuration
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([
                $source => config_path('otp.php'),
                $templates => base_path('resources/views/vendor/template-otp'),
                $migrations => base_path('database/migrations')
            ]);
        }

        // Lumen Package Configuration
        if ($this->app instanceof LumenApplication) {
            $this->app->configure('otp');
        }

        $this->mergeConfigFrom($source, 'otp');

        // Migrate the OTPs tables
        // $this->migrateTables();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(OtpValidator::class, function () {
            return new OtpValidator();
        });
    }

    /**
     * Add tables for migration.
     *
     * @return void
     */
    private function migrateTables()
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
