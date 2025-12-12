<?php

namespace App\Filament\Resources\ServicioResource\Pages;

use App\Filament\Resources\ServicioResource;
use App\Models\Servicio;
use Filament\Resources\Pages\Page;

class ViewServicioReport extends Page
{
    protected static string $resource = ServicioResource::class;
    protected static string $view = 'filament.resources.servicio-resource.pages.view-servicio-report';

    public Servicio $record;

    public function mount($record): void
    {
        $this->record = Servicio::findOrFail($record);
    }

    public function getTitle(): string
    {
        return 'Prefactura - ' . ($this->record->placa ?? 'N/A');
    }

    public function getProductos(): array
    {
        $productos = $this->record->productos;
        
        // Si es string (JSON sin decodificar), decodifica
        if (is_string($productos)) {
            $decoded = json_decode($productos, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        // Si ya es array, devuelve
        if (is_array($productos)) {
            return $productos;
        }
        
        return [];
    }

    public function getServicios(): array
    {
        $servicios = $this->record->servicios;
        
        // Si es string (JSON sin decodificar), decodifica
        if (is_string($servicios)) {
            $decoded = json_decode($servicios, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        // Si ya es array, devuelve
        if (is_array($servicios)) {
            return $servicios;
        }
        
        return [];
    }

    public function getTotalProductos(): float
    {
        $total = 0;
        foreach ($this->getProductos() as $producto) {
            $cantidad = $producto['cantidad'] ?? 0;
            $total += (float)$cantidad;
        }
        return $total;
    }

    public function getTotalServicios(): float
    {
        $total = 0;
        foreach ($this->getServicios() as $servicio) {
            $precio = (float)($servicio['servicio_precio'] ?? 0);
            $cantidad = (float)($servicio['servicio_cantidad'] ?? 1);
            $total += ($precio * $cantidad);
        }
        return $total;
    }

    public function getGrandTotal(): float
    {
        return $this->getTotalProductos() + $this->getTotalServicios();
    }
}
