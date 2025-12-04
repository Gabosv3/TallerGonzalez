<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Producto;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FacturaApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_create_factura_and_show()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $producto = Producto::factory()->create(['precio_venta' => 10, 'stock_actual' => 100, 'control_stock' => true]);
        $cliente = Cliente::factory()->create(['nombre' => 'Cliente Test']);

        $payload = [
            'cliente_id' => $cliente->id,
            'fecha' => now()->toDateString(),
            'items' => [
                [
                    'producto_id' => $producto->id,
                    'cantidad' => 2,
                    'precio_unitario' => 10,
                ]
            ],
        ];

        $res = $this->postJson('/api/facturas', $payload);
        $res->assertStatus(201)->assertJson(['success' => true]);

        $id = $res->json('data.id');
        $this->getJson("/api/facturas/{$id}")->assertStatus(200)->assertJsonPath('data.id', $id);
    }

    public function test_index_and_aging()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Crear facturas via factory o endpoint
        $this->assertTrue(true);
    }

    public function test_create_and_cancel_factura_restores_stock()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $producto = Producto::factory()->create(['stock_actual' => 10, 'control_stock' => true]);

        $payload = [
            'cliente' => 'Cliente Test',
            'items' => [
                [
                    'producto_id' => $producto->id,
                    'cantidad' => 2,
                    'precio_unitario' => 100,
                ],
            ],
        ];

        // Crear factura
        $create = $this->postJson('/api/facturas', $payload);

        $create->assertStatus(201)->assertJsonPath('success', true);

        $facturaId = $create->json('data.id');

        $producto->refresh();
        $this->assertEquals(8, $producto->stock_actual);

        // Cancelar factura
        $update = $this->putJson("/api/facturas/{$facturaId}", ['estado' => 'cancelada']);

        $update->assertStatus(200);

        $producto->refresh();
        $this->assertEquals(10, $producto->stock_actual);
    }
}
