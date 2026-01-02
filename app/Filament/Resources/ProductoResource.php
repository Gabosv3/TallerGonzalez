<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductoResource\Pages;
use App\Models\Producto;
use App\Models\TipoProducto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class ProductoResource extends Resource
{
    protected static ?string $model = Producto::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Inventario Automotriz';
    protected static ?string $navigationLabel = 'Productos';
    protected static ?string $modelLabel = 'Producto';
    protected static ?string $pluralModelLabel = 'Productos';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        // Sección de información básica
                        Forms\Components\Section::make('Información del Producto')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre del Producto')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej: Aceite Motor 5W-30 Sintético')
                                    ->columnSpanFull(),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('codigo')
                                            ->label('Código SKU')
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(50)
                                            ->placeholder('PROD-001')
                                            ->helperText('Código único del producto'),

                                        Forms\Components\Select::make('marca_id')
                                            ->label('Marca')
                                            ->relationship('marca', 'nombre')
                                            ->searchable()
                                            ->preload()
                                            ->nullable(),
                                    ]),

                                Forms\Components\Select::make('tipo_producto_id')
                                    ->label('Tipo de Producto')
                                    ->relationship('tipoProducto', 'nombre')
                                    ->required()
                                    ->live()
                                    ->preload()
                                    ->searchable()
                                    ->helperText('Selecciona el tipo de producto')
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('descripcion')
                                    ->label('Descripción')
                                    ->rows(3)
                                    ->placeholder('Descripción detallada del producto...')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),

                        // Sección de especificaciones generales
                        Forms\Components\Section::make('Especificaciones Técnicas')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Select::make('unidad_medida')
                                    ->label('Unidad de Medida')
                                    ->options([
                                        'pza' => 'Pieza',
                                        'kg' => 'Kilogramo',
                                        'l' => 'Litro',
                                        'ml' => 'Mililitro',
                                        'gal' => 'Galón',
                                        'caja' => 'Caja',
                                        'par' => 'Par',
                                    ])
                                    ->default('pza')
                                    ->required(),

                                Forms\Components\Textarea::make('especificaciones_generales')
                                    ->label('Especificaciones Técnicas')
                                    ->rows(4)
                                    ->placeholder('Especificaciones generales del producto...')
                                    ->helperText('Información técnica adicional')
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->collapsed(fn($record) => !($record && $record->es_aceite)),

                        // Sección para mostrar información de variantes de aceite
                        Forms\Components\Section::make('Variantes de Aceite')
                            ->description('Detalles técnicos de las presentaciones de aceite vinculadas')
                            ->icon('heroicon-o-beaker')
                            ->schema([
                                Forms\Components\Placeholder::make('info_variantes')
                                    ->label('')
                                    ->content(function ($record) {
                                        if (!$record) {
                                            return new HtmlString('
                                                <div class="text-center p-4 bg-gray-50 border border-gray-200 rounded">
                                                    <p class="text-sm text-gray-600">Guarda el producto primero para ver las variantes</p>
                                                </div>
                                            ');
                                        }

                                        // Forzamos la carga de la relación si no está
                                        if (!$record->relationLoaded('tipoProducto')) {
                                            $record->load('tipoProducto');
                                        }

                                        if (!$record->es_aceite) {
                                            return new HtmlString('
                                                <div class="text-center p-4 bg-gray-50 border border-gray-200 rounded">
                                                    <p class="text-sm text-gray-600">Este producto no es un aceite</p>
                                                </div>
                                            ');
                                        }

                                        $variantes = $record->info_variantes;

                                        if ($variantes->isEmpty()) {
                                            return null;
                                        }

                                        $html = '<div class="space-y-4">';

                                        foreach ($variantes as $index => $variante) {
                                            $html .= "
                                                <div class='p-4 bg-white border border-gray-200 rounded-xl shadow-sm'>
                                                    <div class='flex items-center gap-2 mb-4 pb-2 border-b border-gray-100'>
                                                        <div class='p-1.5 bg-blue-50 rounded-lg'>
                                                            <svg class='w-5 h-5 text-blue-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                                                                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.628.282a2 2 0 01-1.806 0l-.628-.282a6 6 0 00-3.86-.517l-2.387.477a2 2 0 00-1.022.547V19a2 2 0 002 2h11a2 2 0 002-2v-3.572zM15 11V5a2 2 0 10-4 0v6m-4 1v1m12-1v1m-12 4v1m12-1v1' />
                                                            </svg>
                                                        </div>
                                                        <p class='font-bold text-gray-900'>{$variante['marca']} - {$variante['tipo_aceite']}</p>
                                                    </div>

                                                    <div class='grid grid-cols-2 md:grid-cols-4 gap-6'>
                                                        <div class='space-y-1'>
                                                            <p class='text-[10px] font-bold text-gray-400 uppercase tracking-widest'>Viscosidad SAE</p>
                                                            <p class='text-sm text-gray-900 font-semibold bg-gray-50 p-2 rounded-lg border border-gray-100'>{$variante['viscosidad']}</p>
                                                        </div>
                                                        <div class='space-y-1'>
                                                            <p class='text-[10px] font-bold text-gray-400 uppercase tracking-widest'>Capacidad</p>
                                                            <p class='text-sm text-gray-900 font-semibold bg-gray-50 p-2 rounded-lg border border-gray-100'>{$variante['capacidad_ml']} ml</p>
                                                        </div>
                                                        <div class='space-y-1'>
                                                            <p class='text-[10px] font-bold text-gray-400 uppercase tracking-widest'>Presentación</p>
                                                            <p class='text-sm text-gray-900 font-semibold bg-gray-50 p-2 rounded-lg border border-gray-100'>{$variante['presentacion']}</p>
                                                        </div>
                                                        <div class='space-y-1'>
                                                            <p class='text-[10px] font-bold text-gray-400 uppercase tracking-widest'>Equivalencia</p>
                                                            <p class='text-sm text-blue-700 font-bold bg-blue-50 p-2 rounded-lg border border-blue-100'>{$variante['capacidad']}</p>
                                                        </div>
                                                    </div>
                                                    
                                                    " . ($variante['especificaciones']['norma_api'] || $variante['especificaciones']['norma_acea'] ? "
                                                    <div class='mt-4 pt-3 border-t border-gray-50 flex flex-wrap gap-2'>
                                                        " . ($variante['especificaciones']['norma_api'] ?
                                                '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">API: ' . $variante['especificaciones']['norma_api'] . '</span>' : '') . "
                                                        " . ($variante['especificaciones']['norma_acea'] ?
                                                '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">ACEA: ' . $variante['especificaciones']['norma_acea'] . '</span>' : '') . "
                                                    </div>
                                                    " : '') . "
                                                </div>
                                            ";
                                        }
                                        $html .= '</div>';

                                        return new HtmlString($html);
                                    })
                            ])
                            ->visible(fn($record) => $record && $record->es_aceite && $record->aceites()->exists())
                            ->collapsible()
                            ->collapsed(false),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        // Sección de precios
                        Forms\Components\Section::make('Precios y Costos')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\TextInput::make('precio_compra')
                                    ->label('Precio Compra')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->required()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state !== null && $state !== '') {
                                            $set('precio_compra_con_iva', round($state * 1.13, 2));
                                        }
                                    }),

                                Forms\Components\TextInput::make('precio_compra_con_iva')
                                    ->label('Compra + IVA (13%)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->live(debounce: 500)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->precio_compra) {
                                            $component->state(round($record->precio_compra * 1.13, 2));
                                        }
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state !== null && $state !== '') {
                                            $set('precio_compra', round($state / 1.13, 2));
                                        }
                                    }),

                                Forms\Components\TextInput::make('precio_venta')
                                    ->label('Precio Venta')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->required()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state !== null && $state !== '') {
                                            $set('precio_venta_con_iva', round($state * 1.13, 2));
                                        }
                                    }),

                                Forms\Components\TextInput::make('precio_venta_con_iva')
                                    ->label('Venta + IVA (13%)')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->live(debounce: 500)
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record && $record->precio_venta) {
                                            $component->state(round($record->precio_venta * 1.13, 2));
                                        }
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state !== null && $state !== '') {
                                            $set('precio_venta', round($state / 1.13, 2));
                                        }
                                    }),

                                Forms\Components\TextInput::make('precio_minimo')
                                    ->label('Precio Mínimo')
                                    ->numeric()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->helperText('Precio mínimo permitido'),
                            ]),

                        // Sección de inventario
                        Forms\Components\Section::make('Inventario y Estado')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Forms\Components\TextInput::make('stock_actual')
                                    ->label('Stock Actual')
                                    ->numeric()
                                    ->required()
                                    ->default(0),

                                Forms\Components\TextInput::make('stock_minimo')
                                    ->label('Stock Mínimo')
                                    ->numeric()
                                    ->required()
                                    ->default(0),

                                Forms\Components\TextInput::make('stock_maximo')
                                    ->label('Stock Máximo')
                                    ->numeric()
                                    ->nullable(),

                                Forms\Components\Toggle::make('control_stock')
                                    ->label('Controlar Stock')
                                    ->default(true)
                                    ->inline(false),

                                Forms\Components\Toggle::make('activo')
                                    ->label('Producto Activo')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->persistSearchInSession()
            ->columns([
                // Columna con badge de tipo
                Tables\Columns\TextColumn::make('tipoProducto.nombre')
                    ->label('Tipo')
                    ->badge()
                    ->color(function ($record) {
                        $tipo = $record->tipoProducto?->nombre;
                        if ($tipo === 'aceite') return 'success';
                        if ($tipo === 'normal') return 'gray';
                        if ($tipo === 'bateria') return 'warning';
                        return 'primary';
                    })
                    ->sortable()
                    ->searchable(),

                // Columna de código con icono
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable(['codigo', 'nombre'])
                    ->sortable()
                    ->description(function ($record) {
                        $desc = $record->nombre;
                        // Si es aceite y tiene variantes, mostrar información
                        if ($record->es_aceite && $record->tiene_variantes) {
                            $totalVariantes = $record->aceites->count();
                            $stockTotal = $record->aceites->sum('stock_disponible');
                            $desc .= " 📦 ({$totalVariantes} variantes, {$stockTotal} total)";
                        } elseif ($record->es_aceite && $record->variante_principal) {
                            $variante = $record->variante_principal;
                            $desc .= " - {$variante->marca->nombre} {$variante->viscosidad}";
                        }
                        return $desc;
                    })
                    ->weight('bold')
                    ->icon('heroicon-o-qr-code')
                    ->iconColor('gray'),

                // Columna de marca
                Tables\Columns\TextColumn::make('marca.nombre')
                    ->label('Marca')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Sin marca'),

                // Columna de precios
                Tables\Columns\TextColumn::make('precio_venta')
                    ->label('Precio Venta')
                    ->money('USD')
                    ->sortable()
                    ->color('success')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('precio_compra')
                    ->label('Precio Compra')
                    ->money('USD')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Columna de stock con indicador visual
                Tables\Columns\TextColumn::make('stock_actual')
                    ->label('Stock')
                    ->sortable()
                    ->color(function ($record) {
                        if ($record->stock_actual <= 0) return 'danger';
                        if ($record->stock_actual <= $record->stock_minimo) return 'warning';
                        return 'success';
                    })
                    ->weight('bold')
                    ->formatStateUsing(fn($record) => "{$record->stock_actual} {$record->unidad_medida}")
                    ->description(fn($record) => "Mín: {$record->stock_minimo}"),

                // Columna de estado con toggle rápido
                Tables\Columns\ToggleColumn::make('activo')
                    ->label('Activo')
                    ->sortable(),

                // Columna de control stock
                Tables\Columns\IconColumn::make('control_stock')
                    ->label('Control Stock')
                    ->boolean()
                    ->trueIcon('heroicon-o-clipboard-document-check')
                    ->falseIcon('heroicon-o-clipboard-document')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Fechas
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Mostrar registros borrados (soft deletes)
                Tables\Filters\TrashedFilter::make(),

                // Filtro por tipo de producto
                Tables\Filters\SelectFilter::make('tipo_producto_id')
                    ->label('Tipo de Producto')
                    ->relationship('tipoProducto', 'nombre')
                    ->preload()
                    ->multiple(),

                // Filtro por marca
                Tables\Filters\SelectFilter::make('marca_id')
                    ->label('Marca')
                    ->relationship('marca', 'nombre')
                    ->preload()
                    ->searchable(),

                // Filtro de Stock Bajo
                Tables\Filters\Filter::make('stock_bajo')
                    ->label('Stock Bajo')
                    ->query(fn(Builder $query) => $query->whereColumn('stock_actual', '<=', 'stock_minimo'))
                    ->indicator('Stock Bajo'),

                // Filtro por estado
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado Activo')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),

                // Nuevo filtro: Productos con variantes
                Tables\Filters\Filter::make('con_variantes')
                    ->label('Con Variantes')
                    ->query(fn(Builder $query) => $query->whereHas('aceites', function ($q) {
                        $q->groupBy('producto_id')
                            ->havingRaw('COUNT(*) > 1');
                    }))
                    ->toggle(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('reporte_reorden_pdf')
                    ->label('Reporte Reorden (PDF)')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->url(fn() => route('productos.reporte_reorden'))
                    ->openUrlInNewTab()
                    ->color('warning'),

                Tables\Actions\Action::make('reporte_general')
                    ->label('Reporte General PDF')
                    ->icon('heroicon-o-document-text')
                    ->url(fn() => route('productos.reporte_general'))
                    ->openUrlInNewTab()
                    ->color('primary'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('primary'),

                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning'),

                    Tables\Actions\Action::make('gestionarVariantes')
                        ->label('Gestionar Variantes')
                        ->icon('heroicon-o-beaker')
                        ->color('success')
                        ->url(fn($record) => \App\Filament\Resources\AceiteResource::getUrl('index', ['tableFilters[producto_id][value]' => $record->id]))
                        ->visible(fn($record) => $record->es_aceite),

                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),

                    Tables\Actions\RestoreAction::make()
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success'),
                ])
                    ->button()
                    ->label('Acciones')
                    ->color('gray')
                    ->size('sm'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),

                    Tables\Actions\BulkAction::make('activar')
                        ->label('Activar seleccionados')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['activo' => true])),

                    Tables\Actions\BulkAction::make('desactivar')
                        ->label('Desactivar seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn($records) => $records->each->update(['activo' => false])),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Crear Producto')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('tipoProducto.nombre')
                    ->label('Tipo de Producto')
                    ->collapsible(),

                Tables\Grouping\Group::make('activo')
                    ->label('Estado')
                    ->collapsible(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductos::route('/'),
            'create' => Pages\CreateProducto::route('/create'),
            'edit' => Pages\EditProducto::route('/{record}/edit'),
        ];
    }


    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('activo', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['codigo', 'nombre', 'descripcion', 'marca.nombre'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->nombre;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['tipoProducto', 'marca', 'aceites.marca', 'aceites.tipoAceite']);
    }
}
