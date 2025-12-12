<?php

namespace App\Filament\Resources\ServicioResource\Pages;

use App\Filament\Resources\ServicioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditServicio extends EditRecord
{
    protected static string $resource = ServicioResource::class;

    protected static bool $canDeleteRecords = true;

    // Oculta las acciones de guardar para experiencia auto-guardado
    protected function getFormActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }

    protected function afterFill(): void
    {
        // Después de cargar el formulario
    }

    protected function beforeValidate(): void
    {
        // Antes de validar, guardar automáticamente
        $this->saveFormData();
    }

    // Auto-guardar ante cualquier cambio en el formulario
    public function updated($property, $value): void
    {
        // Intento de guardado ligero; errores se ignoran para no interrumpir
        $this->saveFormData();
    }

    private function saveFormData(): void
    {
        try {
            $state = $this->form->getState();
            $state['productos'] = $state['productos'] ?? [];
            $state['servicios'] = $state['servicios'] ?? [];

            // Obtener datos anteriores
            $previousProducts = $this->record->productos ?? [];
            $previousServices = $this->record->servicios ?? [];

            // Guardar el registro directamente
            $this->record->update($state);

            // Detectar nuevos productos
            if (count($state['productos']) > count($previousProducts)) {
                $nuevoProducto = end($state['productos']);
                if (isset($nuevoProducto['nombre_producto'])) {
                    Notification::make()
                        ->title('✅ Producto Agregado')
                        ->body('📦 ' . $nuevoProducto['nombre_producto'] . ' (' . ($nuevoProducto['cantidad'] ?? 1) . 'x)')
                        ->success()
                        ->duration(2000)
                        ->send();
                }
            } elseif (count($state['productos']) === count($previousProducts) && count($previousProducts) > 0) {
                // Detectar cambios en productos existentes
                foreach ($state['productos'] as $index => $producto) {
                    if (isset($previousProducts[$index]) && $previousProducts[$index] !== $producto) {
                        Notification::make()
                            ->title('✏️ Producto Actualizado')
                            ->body('📦 ' . ($producto['nombre_producto'] ?? 'Producto') . ' (' . ($producto['cantidad'] ?? 1) . 'x)')
                            ->info()
                            ->duration(2000)
                            ->send();
                        break;
                    }
                }
            }

            // Detectar nuevos servicios
            if (count($state['servicios']) > count($previousServices)) {
                $nuevoServicio = end($state['servicios']);
                if (isset($nuevoServicio['servicio_nombre'])) {
                    Notification::make()
                        ->title('✅ Servicio Agregado')
                        ->body('🔧 ' . $nuevoServicio['servicio_nombre'] . ' - $' . ($nuevoServicio['servicio_precio'] ?? 0))
                        ->success()
                        ->duration(2000)
                        ->send();
                }
            } elseif (count($state['servicios']) === count($previousServices) && count($previousServices) > 0) {
                // Detectar cambios en servicios existentes
                foreach ($state['servicios'] as $index => $servicio) {
                    if (isset($previousServices[$index]) && $previousServices[$index] !== $servicio) {
                        Notification::make()
                            ->title('✏️ Servicio Actualizado')
                            ->body('🔧 ' . ($servicio['servicio_nombre'] ?? 'Servicio') . ' - $' . ($servicio['servicio_precio'] ?? 0))
                            ->info()
                            ->duration(2000)
                            ->send();
                        break;
                    }
                }
            }

        } catch (\Throwable $e) {
            // Ignorar errores silenciosamente
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['productos'] = $data['productos'] ?? [];
        $data['servicios'] = $data['servicios'] ?? [];

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
