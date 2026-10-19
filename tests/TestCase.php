<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Carga los roles base con sus identificadores canónicos antes de cada
     * ciclo de pruebas con RefreshDatabase. El sistema usa IDs fijos para los
     * roles Cliente (2), Delivery (3) y Cocinero (4).
     */
    protected $seed = true;
}
