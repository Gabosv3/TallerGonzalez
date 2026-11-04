<?php

namespace App\Services;

use App\Filament\Resources\ClienteResource;
use App\Filament\Resources\ProductoResource;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Producto;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Notificaciones internas del panel (campana). Nunca deben interrumpir la operación que las origina.
 */
class Notificaciones
{
    /** Roles que siempre reciben las notificaciones. */
    private const ROLES_BASE = ['super_admin', 'admin'];

    public static function stockBajo(Producto $producto): void
    {
        $agotado = $producto->stock_actual <= 0;

        self::enviar(
            titulo: $agotado ? 'Producto agotado' : 'Stock bajo',
            cuerpo: $agotado
                ? "{$producto->nombre} ({$producto->codigo}) se quedó sin existencias."
                : "{$producto->nombre} ({$producto->codigo}): quedan {$producto->stock_actual} unidades, el mínimo es {$producto->stock_minimo}.",
            icono: $agotado ? 'heroicon-o-x-circle' : 'heroicon-o-exclamation-triangle',
            color: $agotado ? 'danger' : 'warning',
            accion: ['Ver producto', fn () => ProductoResource::getUrl('edit', ['record' => $producto])],
            roles: ['almacen'],
        );
    }

    public static function ventaRegistrada(Factura $factura): void
    {
        $cliente = $factura->getRelation('cliente') ?? $factura->cliente()->first();
        $nombre = $cliente ? ($cliente->razon_social ?: $cliente->nombre_completo) : ($factura->getRawOriginal('cliente') ?: 'Consumidor final');

        self::enviar(
            titulo: 'Nueva venta registrada',
            cuerpo: "Factura {$factura->numero_factura} · {$nombre} · $" . number_format((float) $factura->total, 2),
            icono: 'heroicon-o-banknotes',
            color: 'success',
            accion: ['Ver factura', fn () => route('facturas.reporte.pdf', $factura), true],
            roles: ['vendedor'],
        );
    }

    public static function clienteNuevo(Cliente $cliente): void
    {
        self::enviar(
            titulo: 'Nuevo cliente',
            cuerpo: ($cliente->razon_social ?: $cliente->nombre_completo) . ' fue agregado al sistema.',
            icono: 'heroicon-o-user-plus',
            color: 'info',
            accion: ['Ver cliente', fn () => ClienteResource::getUrl('edit', ['record' => $cliente])],
            roles: ['vendedor'],
        );
    }

    /**
     * @param  array{0: string, 1: \Closure, 2?: bool}|null  $accion  [etiqueta, url, nueva pestaña]
     */
    private static function enviar(string $titulo, string $cuerpo, string $icono, string $color, ?array $accion = null, array $roles = []): void
    {
        try {
            $destinatarios = User::whereHas('roles', fn ($q) => $q->whereIn('name', array_merge(self::ROLES_BASE, $roles)))->get();

            foreach ($destinatarios as $usuario) {
                $notificacion = Notification::make()
                    ->title($titulo)
                    ->body($cuerpo)
                    ->icon($icono)
                    ->iconColor($color);

                if ($accion) {
                    $notificacion->actions([
                        Action::make('ver')
                            ->label($accion[0])
                            ->url(($accion[1])(), shouldOpenInNewTab: $accion[2] ?? false)
                            ->markAsRead(),
                    ]);
                }

                $notificacion->sendToDatabase($usuario);
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar la notificación: ' . $e->getMessage());
        }
    }
}
