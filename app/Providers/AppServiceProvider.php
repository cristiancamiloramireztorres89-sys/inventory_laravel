<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registrar cualquier servicio de la aplicación.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializar cualquier servicio de la aplicación (arranque).
     */
    public function boot(): void
    {
        $isHttps = app()->environment('production') || str_contains(request()->header('x-forwarded-proto', ''), 'https');

        if ($isHttps) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            config([
                'session.secure' => true,
                'session.domain' => null,
            ]);
        }

        // Registrar transporte de correo Brevo API (HTTPS puerto 443)
        \Illuminate\Support\Facades\Mail::extend('brevo', function (array $config = []) {
            $key = config('services.brevo.key') ?? env('BREVO_API_KEY');
            return (new \Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory())->create(
                new \Symfony\Component\Mailer\Transport\Dsn('brevo+api', 'default', $key)
            );
        });
    }
}
