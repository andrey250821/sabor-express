<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

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
        // Resolver la configuración al renderizar la primera vista, no al
        // arrancar Laravel: las pruebas pueden iniciar antes de crear tablas.
        View::composer('*', function ($view): void {
            $app = app();

            if (! $app->bound('sabor-express.configuracion.resolved')) {
                $configuracion = Schema::hasTable('configuraciones')
                    ? Configuracion::first()
                    : null;

                $app->instance('sabor-express.configuracion', $configuracion);
                $app->instance('sabor-express.configuracion.resolved', true);
            }

            $view->with(
                'configuracion',
                $app->make('sabor-express.configuracion')
            );
        });
    }
}