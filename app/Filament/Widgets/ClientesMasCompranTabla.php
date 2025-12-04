<?php

namespace App\Filament\Widgets;

use App\Models\Cliente;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ClientesMasCompranTabla extends BaseWidget
{
    protected static ?string $heading = '👨‍💼 Clientes Más Compradores - Detalle';
    protected static ?int $sort = 5;

    protected function getTableQuery(): Builder
    {
        $mesActual = Carbon::now();
        $inicioMes = $mesActual->copy()->startOfMonth();
        $finMes = $mesActual->copy()->endOfMonth();

        return Cliente::query()
            ->selectRaw('clientes.id, clientes.nombre, clientes.email, clientes.telefono,
                COUNT(facturas.id) as total_facturas,
                SUM(facturas.total) as total_gasto')
            ->join('facturas', 'clientes.id', '=', 'facturas.cliente_id')
            ->whereBetween('facturas.created_at', [$inicioMes, $finMes])
            ->groupBy('clientes.id', 'clientes.nombre', 'clientes.email', 'clientes.telefono')
            ->orderByDesc('total_gasto')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('nombre')
                ->label('Cliente')
                ->sortable()
                ->searchable()
                ->limit(25),
            TextColumn::make('email')
                ->label('Email')
                ->sortable()
                ->searchable()
                ->limit(20),
            TextColumn::make('telefono')
                ->label('Teléfono')
                ->default('N/A')
                ->limit(15),
            BadgeColumn::make('total_facturas')
                ->label('Facturas')
                ->color('warning'),
            TextColumn::make('total_gasto')
                ->label('Total Gastado')
                ->formatStateUsing(fn ($state) => '$' . number_format($state, 2))
                ->sortable(),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10];
    }
}
