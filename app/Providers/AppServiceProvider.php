<?php

namespace App\Providers;

use App\Models\Configuracion;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // La aplicación también debe arrancar cuando las migraciones todavía
        // no han creado la tabla configuraciones (por ejemplo, en PHPUnit).
        View::composer('*', function ($view): void {
            $configuracion = Schema::hasTable('configuraciones')
                ? Configuracion::query()->first()
                : null;

            $view->with('configuracion', $configuracion);
        });
    }
}
