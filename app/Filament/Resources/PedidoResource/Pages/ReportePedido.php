<?php

namespace App\Filament\Resources\PedidoResource\Pages;

use App\Filament\Resources\PedidoResource;
use App\Models\Pedido;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportePedido extends ViewRecord
{
    protected static string $resource = PedidoResource::class;

    protected static string $view = 'filament.resources.pedido-resource.pages.reporte-pedido';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('descargar_pdf')
                ->label('Descargar PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->action('descargarPDF'),
            
            Actions\Action::make('imprimir')
                ->label('Imprimir')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action('imprimirReporte'),
            
            Actions\Action::make('volver')
                ->label('Volver a Pedidos')
                ->icon('heroicon-o-arrow-left')
                ->color('success')
                ->url(PedidoResource::getUrl()),
        ];
    }

    public function descargarPDF(): StreamedResponse
    {
        $pedido = $this->record->load(['proveedor', 'detalles.producto']);
        
        $pdf = Pdf::loadView('filament.resources.pedido-resource.pages.reporte-pdf', [
            'pedido' => $pedido
        ]);

        $filename = "orden_compra_{$pedido->numero_factura}.pdf";

        return response()->streamDownload(
            function () use ($pdf) {
                echo $pdf->output();
            },
            $filename
        );
    }

    public function imprimirReporte(): void
    {
        $this->dispatch('print');
    }
}