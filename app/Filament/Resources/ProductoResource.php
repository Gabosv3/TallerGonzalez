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
                            ->collapsed(),

                        // Sección para mostrar información de variantes de aceite
                        Forms\Components\Section::make('Variantes de Aceite')
                            ->icon('heroicon-o-beaker')
                            ->schema([
                                Forms\Components\Placeholder::make('info_variantes')
                                    ->label('')
                                    ->content(function ($record) {
                                        if (!$record || !$record->es_aceite) {
                                            return new HtmlString('
                                                <div class="text-center p-4 bg-gray-50 border border-gray-200 rounded">
                                                    <p class="text-sm text-gray-600">
                                                        ' . (!$record ? 'Guarda el producto primero' : 
                                                            'Este producto no es un aceite') . '
                                                    </p>
                                                </div>
                                            ');
                                        }

                                        $variantes = $record->info_variantes;

                                        if ($variantes->isEmpty()) {
                                            return new HtmlString('
                                                <div class="text-center p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
                                                    <p class="text-sm text-yellow-700 font-medium">
                                                        Este producto aceite no tiene variantes registradas
                                                    </p>
                                                    <p class="text-xs text-yellow-600 mt-2">
                                                        Ve al módulo de Aceites para agregar variantes
                                                    </p>
                                                    <div class="mt-3">
                                                        <a href="' . \App\Filament\Resources\AceiteResource::getUrl('create') . '" 
                                                           class="inline-flex items-center px-3 py-2 text-xs font-medium text-yellow-700 bg-yellow-100 rounded hover:bg-yellow-200">
                                                            <span class="mr-1">➕</span>
                                                            Agregar Variante
                                                        </a>
                                                    </div>
                                                </div>
                                            ');
                                        }

                                        // Calcular stock total de todas las variantes
                                        $stockTotal = $variantes->sum('stock_disponible');
                                        $stockTotalColor = $stockTotal == 0 ? 'text-red-600' : 
                                                         ($stockTotal <= 10 ? 'text-orange-600' : 'text-green-600');

                                        $html = "
                                            <div class='mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg'>
                                                <div class='flex justify-between items-center'>
                                                    <div>
                                                        <h4 class='font-semibold text-blue-900'>Resumen de Variantes</h4>
                                                        <p class='text-sm text-blue-700'>Total de variantes: <span class='font-medium'>{$variantes->count()}</span></p>
                                                    </div>
                                                    <div class='text-right'>
                                                        <p class='text-sm text-blue-700'>Stock Total:</p>
                                                        <p class='text-2xl font-bold {$stockTotalColor}'>{$stockTotal}</p>
                                                        <p class='text-xs text-blue-600'>unidades disponibles</p>
                                                    </div>
                                                </div>
                                            </div>
                                        ";

                                        $html .= '<div class="space-y-3">';
                                        
                                        foreach ($variantes as $index => $variante) {
                                            $borderColor = $variante['stock_disponible'] == 0 ? 'border-red-200' : 
                                                         ($variante['stock_disponible'] <= 5 ? 'border-orange-200' : 'border-green-200');
                                            $bgColor = $variante['stock_disponible'] == 0 ? 'bg-red-50' : 
                                                     ($variante['stock_disponible'] <= 5 ? 'bg-orange-50' : 'bg-green-50');
                                            $statusIcon = $variante['stock_disponible'] == 0 ? '🔴' : 
                                                        ($variante['stock_disponible'] <= 5 ? '🟡' : '🟢');

                                            // Precio individual de la variante (si está disponible, sino usar precio del producto)
                                            $precioIndividual = $variante['precio_venta'] ?? $record->precio_venta;
                                            $precioCompra = $variante['precio_compra'] ?? $record->precio_compra;

                                            $html .= "
                                                <div class='p-4 border rounded-lg {$borderColor} {$bgColor}'>
                                                    <div class='flex justify-between items-start'>
                                                        <div class='flex-1'>
                                                            <div class='flex items-center gap-2 mb-2'>
                                                                <span class='text-lg'>{$statusIcon}</span>
                                                                <p class='font-semibold text-gray-900'>{$variante['marca']} {$variante['viscosidad']}</p>
                                                                " . (!$variante['activo'] ? 
                                                                '<span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full font-medium">INACTIVO</span>' : '') . "
                                                            </div>
                                                            
                                                            <div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 text-sm'>
                                                                <div class='flex items-center gap-1'>
                                                                    <span class='font-medium text-gray-700'>Tipo:</span>
                                                                    <span class='text-gray-900'>{$variante['tipo_aceite']}</span>
                                                                </div>
                                                                <div class='flex items-center gap-1'>
                                                                    <span class='font-medium text-gray-700'>Capacidad:</span>
                                                                    <span class='text-gray-900'>{$variante['capacidad']}</span>
                                                                </div>
                                                                <div class='flex items-center gap-1'>
                                                                    <span class='font-medium text-gray-700'>Presentación:</span>
                                                                    <span class='text-gray-900'>{$variante['presentacion']}</span>
                                                                </div>
                                                                <div class='flex items-center gap-1'>
                                                                    <span class='font-medium text-gray-700'>Stock:</span>
                                                                    <span class='text-gray-900 font-semibold'>{$variante['stock_disponible']} unidades</span>
                                                                </div>
                                                            </div>
                                                            
                                                            " . ($variante['especificaciones']['norma_api'] || $variante['especificaciones']['norma_acea'] ? "
                                                            <div class='mt-2 flex flex-wrap gap-2'>
                                                                " . ($variante['especificaciones']['norma_api'] ? 
                                                                '<span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">API: ' . $variante['especificaciones']['norma_api'] . '</span>' : '') . "
                                                                " . ($variante['especificaciones']['norma_acea'] ? 
                                                                '<span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded">ACEA: ' . $variante['especificaciones']['norma_acea'] . '</span>' : '') . "
                                                                " . ($variante['especificaciones']['viscosidad_sae'] ? 
                                                                '<span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">SAE: ' . $variante['especificaciones']['viscosidad_sae'] . '</span>' : '') . "
                                                            </div>
                                                            " : '') . "
                                                        </div>
                                                        
                                                        <div class='text-right ml-4 min-w-[120px]'>
                                                            <div class='mb-3'>
                                                                <p class='text-2xl font-bold text-gray-900'>{$variante['stock_disponible']}</p>
                                                                <p class='text-xs text-gray-600'>stock actual</p>
                                                            </div>
                                                            <div class='space-y-1'>
                                                                <div>
                                                                    <p class='text-lg font-semibold text-green-600'>\$" . number_format($precioIndividual, 2) . "</p>
                                                                    <p class='text-xs text-gray-500'>precio venta</p>
                                                                </div>
                                                                <div>
                                                                    <p class='text-sm font-medium text-gray-600'>\$" . number_format($precioCompra, 2) . "</p>
                                                                    <p class='text-xs text-gray-500'>precio compra</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            ";
                                        }
                                        $html .= '</div>';

                                        return new HtmlString($html);
                                    })
                            ])
                            ->visible(fn ($record) => $record && $record->es_aceite)
                            ->collapsible()
                            ->collapsed(),
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
                    ->searchable()
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
                    ->formatStateUsing(fn ($record) => "{$record->stock_actual} {$record->unidad_medida}")
                    ->description(fn ($record) => "Mín: {$record->stock_minimo}"),

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
                    ->query(fn (Builder $query) => $query->whereColumn('stock_actual', '<=', 'stock_minimo'))
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
                    ->query(fn (Builder $query) => $query->whereHas('aceites', function ($q) {
                        $q->groupBy('producto_id')
                          ->havingRaw('COUNT(*) > 1');
                    }))
                    ->toggle(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('reporte_reorden_pdf')
                    ->label('Reporte Reorden (PDF)')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->url(fn () => route('productos.reporte_reorden'))
                    ->openUrlInNewTab()
                    ->color('warning'),

                Tables\Actions\Action::make('reporte_general')
                    ->label('Reporte General PDF')
                    ->icon('heroicon-o-document-text')
                    ->url(fn () => route('productos.reporte_general'))
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
                        ->url(fn ($record) => \App\Filament\Resources\AceiteResource::getUrl('index', ['tableFilters[producto_id][value]' => $record->id]))
                        ->visible(fn ($record) => $record->es_aceite),

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
                        ->action(fn ($records) => $records->each->update(['activo' => true])),

                    Tables\Actions\BulkAction::make('desactivar')
                        ->label('Desactivar seleccionados')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['activo' => false])),
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
        $lowStockCount = static::getModel()::whereColumn('stock_actual', '<=', 'stock_minimo')->count();
        return $lowStockCount > 0 ? (string) $lowStockCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
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