<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPedido extends EditRecord
{
    protected static string $resource = PedidoResource::class;

    protected function beforeSave(): void
    {
        $record = $this->record;
        $data = $this->form->getState();

        // Remover campos temporales que no se deben guardar
        if (isset($data['detalles'])) {
            foreach ($data['detalles'] as &$detalle) {
                unset($detalle['precio_con_iva_temp']);
            }
        }

        // Validación 1: No permitir editar completado o cancelado
        if (in_array($record->estado, ['completado', 'cancelado'])) {
            Notification::make()
                ->title('No permitido')
                ->body('No se puede editar un pedido completado o cancelado.')
                ->danger()
                ->send();
            $this->halt();
            return;
        }

        // Validación 2: Validar cambio de estado si es diferente
        if ($data['estado'] !== $record->estado) {
            $estadosValidos = $this->getEstadosValidos($record->estado);

            if (!in_array($data['estado'], $estadosValidos)) {
                Notification::make()
                    ->title('Cambio de estado inválido')
                    ->body('La transición de ' . $record->estado . ' a ' . $data['estado'] . ' no es válida.')
                    ->danger()
                    ->send();
                $this->halt();
                return;
            }
        }

        // Validación 3: Validar fechas solo si existen y son strings
        if (isset($data['fecha_esperada']) && isset($data['fecha_orden'])) {
            if (is_string($data['fecha_esperada']) && is_string($data['fecha_orden'])) {
                if ($data['fecha_esperada'] < $data['fecha_orden']) {
                    Notification::make()
                        ->title('Error de validación')
                        ->body('Fecha esperada debe ser posterior o igual a fecha de orden.')
                        ->danger()
                        ->send();
                    $this->halt();
                    return;
                }
            }
        }
    }

    private function getEstadosValidos(string $estadoActual): array
    {
        return match ($estadoActual) {
            'pendiente' => ['confirmado', 'cancelado'],
            'confirmado' => ['en_camino', 'parcial', 'completado', 'cancelado'],
            'en_camino' => ['parcial', 'completado', 'cancelado'],
            'parcial' => ['completado', 'cancelado'],
            default => [],
        };
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Pedido actualizado')
            ->body('La orden ha sido actualizada correctamente.')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Eliminar orden de compra'),
        ];
    }
}
