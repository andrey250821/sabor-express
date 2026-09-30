<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Tareas programadas
|--------------------------------------------------------------------------
|
| Las notificaciones permanecen visibles durante exactamente 3 días.
| La consulta del modelo las oculta al cumplir el límite y esta tarea
| elimina físicamente las que ya superaron ese tiempo.
|
*/

Schedule::command('notificaciones:limpiar')
    ->hourly()
    ->withoutOverlapping();
