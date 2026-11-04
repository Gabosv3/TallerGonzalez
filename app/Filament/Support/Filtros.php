<?php

namespace App\Filament\Support;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Filtros reutilizables para que todas las tablas se comporten y se lean igual.
 */
class Filtros
{
    /** Activos / Inactivos. */
    public static function activo(string $columna = 'activo'): TernaryFilter
    {
        return TernaryFilter::make($columna)
            ->label('Estado')
            ->placeholder('Todos')
            ->trueLabel('Activos')
            ->falseLabel('Inactivos');
    }

    /** Rango de fechas (desde / hasta) sobre una columna. */
    public static function rangoFechas(string $nombre, string $columna, string $etiqueta): Filter
    {
        return Filter::make($nombre)
            ->label($etiqueta)
            ->form([
                Grid::make(2)->schema([
                    DatePicker::make('desde')->label($etiqueta . ': desde')->native(false)->displayFormat('d/m/Y')->maxDate(fn ($get) => $get('hasta')),
                    DatePicker::make('hasta')->label('Hasta')->native(false)->displayFormat('d/m/Y')->minDate(fn ($get) => $get('desde')),
                ]),
            ])
            ->columnSpan(['default' => 1, 'md' => 2])
            ->query(fn (Builder $query, array $data): Builder => $query
                ->when($data['desde'] ?? null, fn (Builder $q, $fecha) => $q->whereDate($columna, '>=', $fecha))
                ->when($data['hasta'] ?? null, fn (Builder $q, $fecha) => $q->whereDate($columna, '<=', $fecha)))
            ->indicateUsing(function (array $data) use ($etiqueta): array {
                $indicadores = [];
                if ($data['desde'] ?? null) {
                    $indicadores[] = $etiqueta . ' desde ' . Carbon::parse($data['desde'])->format('d/m/Y');
                }
                if ($data['hasta'] ?? null) {
                    $indicadores[] = $etiqueta . ' hasta ' . Carbon::parse($data['hasta'])->format('d/m/Y');
                }

                return $indicadores;
            });
    }

    /** Con / sin registros en una relación (p. ej. marcas con productos). */
    public static function conRelacion(string $nombre, string $relacion, string $etiqueta, string $con = 'Con registros', string $sin = 'Sin registros'): TernaryFilter
    {
        return TernaryFilter::make($nombre)
            ->label($etiqueta)
            ->placeholder('Todos')
            ->trueLabel($con)
            ->falseLabel($sin)
            ->queries(
                true: fn (Builder $query) => $query->has($relacion),
                false: fn (Builder $query) => $query->doesntHave($relacion),
                blank: fn (Builder $query) => $query,
            );
    }

    /** Valores distintos de una columna, para elegir varios. */
    public static function valores(string $columna, string $etiqueta, string $modelo): SelectFilter
    {
        return SelectFilter::make($columna)
            ->label($etiqueta)
            ->multiple()
            ->searchable()
            ->options(fn () => $modelo::query()
                ->whereNotNull($columna)
                ->where($columna, '!=', '')
                ->distinct()
                ->orderBy($columna)
                ->pluck($columna, $columna)
                ->all());
    }

    /** Nivel de stock: agotado, bajo (en o bajo el mínimo) o normal. */
    public static function nivelStock(string $stock = 'stock_actual', string $minimo = 'stock_minimo'): SelectFilter
    {
        return SelectFilter::make('nivel_stock')
            ->label('Nivel de stock')
            ->placeholder('Todos')
            ->options([
                'agotado' => 'Agotado',
                'bajo' => 'Bajo el mínimo',
                'normal' => 'Normal',
            ])
            ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                'agotado' => $query->where($stock, '<=', 0),
                'bajo' => $query->where($stock, '>', 0)->whereColumn($stock, '<=', $minimo),
                'normal' => $query->whereColumn($stock, '>', $minimo),
                default => $query,
            });
    }
}
