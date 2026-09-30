<?php

use Illuminate\Support\Facades\Schedule;

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
