<?php

namespace App\Filament\Resources\ServicioResource\Pages;

use App\Filament\Resources\ServicioResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ViewServicio extends ViewRecord
{
    protected static string $resource = ServicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar')
                ->icon('heroicon-o-pencil'),
            
            Actions\DeleteAction::make()
                ->icon('heroicon-o-trash'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Información del Servicio')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('placa')
                            ->label('Placa del Vehículo')
                            ->icon('heroicon-o-identification')
                            ->copyable()
                            ->columnSpan(1),
                        
                        TextEntry::make('estado')
                            ->label('Estado')
                            ->icon('heroicon-o-check-badge')
                            ->badge()
                            ->formatStateUsing(function ($state) {
                                return match($state) {
                                    'pendiente' => 'Pendiente',
                                    'en_proceso' => 'En Proceso',
                                    'completado' => 'Completado',
                                    'cancelado' => 'Cancelado',
                                    default => $state,
                                };
                            })
                            ->color(function ($state) {
                                return match($state) {
                                    'pendiente' => 'warning',
                                    'en_proceso' => 'info',
                                    'completado' => 'success',
                                    'cancelado' => 'danger',
                                    default => 'gray',
                                };
                            })
                            ->columnSpan(1),
                        
                        TextEntry::make('notas')
                            ->label('Notas')
                            ->markdown()
                            ->columnSpan(2)
                            ->icon('heroicon-o-document-text'),
                        
                        TextEntry::make('created_at')
                            ->label('Fecha de Creación')
                            ->dateTime('d/m/Y H:i')
                            ->icon('heroicon-o-calendar')
                            ->columnSpan(1),
                        
                        TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime('d/m/Y H:i')
                            ->icon('heroicon-o-calendar')
                            ->columnSpan(1),
                    ]),
                
                Section::make('Productos Utilizados')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('productos')
                            ->label('')
                            ->getStateUsing(function ($record) {
                                $productos = $record->productos ?? [];
                                $html = '<table class="w-full border-collapse">';
                                $html .= '<thead><tr class="bg-gray-100">';
                                $html .= '<th class="border p-2 text-left">Producto</th>';
                                $html .= '<th class="border p-2 text-center">Cantidad</th>';
                                $html .= '</tr></thead>';
                                $html .= '<tbody>';
                                
                                foreach ($productos as $item) {
                                    $producto = $item['producto'] ?? 'N/A';
                                    $cantidad = $item['cantidad'] ?? 0;
                                    $html .= "<tr class=\"hover:bg-gray-50\">";
                                    $html .= "<td class=\"border p-2\">$producto</td>";
                                    $html .= "<td class=\"border p-2 text-center\">$cantidad</td>";
                                    $html .= "</tr>";
                                }
                                
                                $html .= '</tbody></table>';
                                return $html;
                            })
                            ->html(),
                    ]),
            ]);
    }
}
