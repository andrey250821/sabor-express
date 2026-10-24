<?php

namespace Tests\Feature\Delivery;

use App\Services\FirebaseDeliveryLocationService;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FirebaseDeliveryLocationTest extends TestCase
{
    public function test_elimina_la_ubicacion_del_pedido_en_firebase(): void
    {
        config([
            'services.firebase.database_url' => 'https://firebase.test.example',
        ]);

        Http::fake([
            '*' => Http::response([], 200),
        ]);

        $resultado = app(FirebaseDeliveryLocationService::class)
            ->eliminarPorPedido(123);

        $this->assertTrue($resultado);

        Http::assertSent(function (HttpRequest $request): bool {
            return $request->method() === 'DELETE'
                && $request->url()
                    === 'https://firebase.test.example/delivery_locations/123.json';
        });
    }

    public function test_devuelve_false_si_firebase_responde_con_error(): void
    {
        config([
            'services.firebase.database_url' => 'https://firebase.test.example',
        ]);

        Http::fake([
            '*' => Http::response(['error' => 'Prueba de error'], 500),
        ]);

        $resultado = app(FirebaseDeliveryLocationService::class)
            ->eliminarPorPedido(456);

        $this->assertFalse($resultado);

        Http::assertSent(function (HttpRequest $request): bool {
            return $request->method() === 'DELETE'
                && $request->url()
                    === 'https://firebase.test.example/delivery_locations/456.json';
        });
    }

    public function test_no_intenta_eliminar_la_ubicacion_si_no_hay_url_configurada(): void
    {
        config([
            'services.firebase.database_url' => '',
        ]);

        Http::fake();

        $resultado = app(FirebaseDeliveryLocationService::class)
            ->eliminarPorPedido(789);

        $this->assertFalse($resultado);

        Http::assertNothingSent();
    }
}
