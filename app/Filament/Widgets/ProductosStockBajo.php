<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductoResource;
use App\Models\Producto;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProductosStockBajo extends BaseWidget
{
    protected static ?string $heading = 'Productos por reabastecer';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Producto::query()->bajoStock()->orderByRaw('stock_actual - stock_minimo'))
            ->columns([
                TextColumn::make('codigo')->label('Código')->searchable(),
                TextColumn::make('nombre')->label('Producto')->limit(40)->searchable(),
                TextColumn::make('stock_actual')
                    ->label('Stock')
                    ->badge()
                    ->color(fn (Producto $record) => $record->stock_actual <= 0 ? 'danger' : 'warning'),
                TextColumn::make('stock_minimo')->label('Mínimo'),
            ])
            ->recordUrl(fn (Producto $record) => ProductoResource::getUrl('edit', ['record' => $record]))
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Inventario al día')
            ->emptyStateDescription('Ningún producto está por debajo del stock mínimo.');
    }
}
