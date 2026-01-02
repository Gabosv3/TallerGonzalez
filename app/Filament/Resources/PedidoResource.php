<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Filament\Resources\PedidoResource\RelationManagers;
use App\Models\Pedido;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Aceite;
use App\Models\PedidoDetalle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Carbon\Carbon;

class PedidoResource extends Resource
{
    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Gestión de Compras';
    protected static ?string $navigationLabel = 'Órdenes de Compra';
    protected static ?string $modelLabel = 'Orden de Compra';
    protected static ?string $pluralModelLabel = 'Órdenes de Compra';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Información Principal')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Section::make('Datos de la Orden')
                                ->description('Información básica de la orden de compra')
                                ->icon('heroicon-o-identification')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('numero_factura')
                                                ->label('Número de Orden')
                                                
                                                ->unique(ignoreRecord: true)
                                                ->validationMessages([
                                                    'unique' => 'Este número de orden ya existe.',
                                                ])
                                                ->maxLength(50)
                                                ->placeholder('PED-' . date('Ymd') . '-0001')
                                                ->disabled()
                                                ->helperText('Se genera automáticamente al guardar')
                                                ->hintIcon('heroicon-o-document'),

                                            Select::make('proveedor_id')
                                                ->label('Proveedor')
                                                ->relationship('proveedor', 'nombre')
                                                ->required()
                                                ->validationMessages([
                                                    'required' => 'Debes seleccionar un proveedor.',
                                                ])
                                                ->searchable()
                                                ->preload()
                                                ->live()
                                                ->afterStateUpdated(function ($state, Set $set) {
                                                    if ($state) {
                                                        $proveedor = Proveedor::find($state);
                                                        if ($proveedor) {
                                                            $set('contacto_proveedor', $proveedor->contacto);
                                                            $set('telefono_proveedor', $proveedor->telefono);
                                                        }
                                                    }
                                                })
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('nombre')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('contacto')
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('telefono')
                                                        ->tel()
                                                        ->maxLength(20),
                                                ])
                                                ->helperText('Selecciona o crea un nuevo proveedor'),
                                        ]),

                                    Grid::make(3)
                                        ->schema([
                                            DatePicker::make('fecha_orden')
                                                ->label('Fecha de Orden')
                                                ->required()
                                                ->default(now())
                                                ->maxDate(now())
                                                ->validationMessages([
                                                    'max_date' => 'La fecha de orden no puede ser futura.',
                                                    'required' => 'La fecha de orden es obligatoria.',
                                                ])
                                                ->helperText('Fecha de emisión de la orden'),

                                            DatePicker::make('fecha_esperada')
                                                ->label('Fecha Esperada')
                                                ->required()
                                                ->minDate(now()->startOfDay())
                                                ->validationMessages([
                                                    'after_or_equal' => 'La fecha esperada debe ser igual o posterior a hoy.',
                                                    'required' => 'La fecha esperada es obligatoria.',
                                                ])
                                                ->default(now()->addDays(7))
                                                ->helperText('Fecha esperada de entrega'),

                                            Select::make('estado')
                                                ->label('Estado del Pedido')
                                                ->options([
                                                    'pendiente' => '🟡 Pendiente',
                                                    'confirmado' => '🔵 Confirmado',
                                                    'en_camino' => '🟠 En Camino',
                                                    'parcial' => '🟣 Parcialmente Recibido',
                                                    'completado' => '🟢 Completado',
                                                    'cancelado' => '🔴 Cancelado',
                                                ])
                                                ->default('pendiente')
                                                ->required()
                                                ->validationMessages([
                                                    'required' => 'El estado es obligatorio.',
                                                ])
                                                ->native(false),
                                        ]),

                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('contacto_proveedor')
                                                ->label('Contacto del Proveedor')
                                                ->maxLength(255)
                                                ->placeholder('Nombre del contacto')
                                                ->helperText('Persona de contacto en el proveedor'),

                                            TextInput::make('telefono_proveedor')
                                                ->label('Teléfono de Contacto')
                                                ->tel()
                                                ->maxLength(20)
                                                ->placeholder('+1 234 567 8900'),
                                        ]),
                                ]),
                        ]),

                    Wizard\Step::make('Detalles de Productos')
                        ->icon('heroicon-o-shopping-bag')
                        ->schema([
                            Section::make('Items del Pedido')
                                ->description('Agrega los productos a incluir en esta orden')
                                ->icon('heroicon-o-list-bullet')
                                ->schema([
                                    Forms\Components\Repeater::make('detalles')
                                        ->relationship('detalles')
                                        ->schema([
                                            Grid::make(5)
                                                ->schema([
                                                    Select::make('producto_id')
                                                        ->label('Producto')
                                                        ->relationship('producto', 'nombre')
                                                        ->required()
                                                        ->validationMessages([
                                                            'required' => 'Selecciona un producto.',
                                                        ])
                                                        ->searchable(['codigo', 'nombre'])
                                                        ->preload()
                                                        ->live()
                                                        ->getSearchResultsUsing(fn (string $search) => Producto::where('codigo', 'LIKE', "%{$search}%")
                                                            ->orWhere('nombre', 'LIKE', "%{$search}%")
                                                            ->limit(50)
                                                            ->pluck('nombre', 'id'))
                                                        ->getOptionLabelUsing(fn ($value) => Producto::find($value)?->nombre ?? $value)
                                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                            if ($state) {
                                                                $producto = Producto::find($state);
                                                                if ($producto) {
                                                                    // Usar precio de compra SIN IVA
                                                                    $set('precio_unitario', $producto->precio_compra);
                                                                    
                                                                    // Calcular y setear precio con IVA
                                                                    $precioConIva = round($producto->precio_compra * 1.13, 2);
                                                                    $set('precio_con_iva_temp', $precioConIva);

                                                                    self::actualizarSubtotal($set, $get);
                                                                }
                                                            }
                                                        })
                                                        ->columnSpan(2),

                                                    TextInput::make('cantidad')
                                                        ->label('Cantidad')
                                                        ->numeric()
                                                        ->required()
                                                        ->default(1)
                                                        ->minValue(1)
                                                        ->validationMessages([
                                                            'required' => 'Ingresa una cantidad.',
                                                            'min' => 'La cantidad debe ser al menos 1.',
                                                        ])
                                                        ->live(onBlur: true)
                                                        ->afterStateUpdated(fn(Set $set, Get $get) => self::actualizarSubtotal($set, $get)),

                                                    TextInput::make('precio_unitario')
                                                        ->label('Precio Sin IVA')
                                                        ->numeric()
                                                        ->required()
                                                        ->validationMessages([
                                                            'required' => 'El precio es obligatorio.',
                                                        ])
                                                        ->prefix('$')
                                                        ->step(0.01)
                                                        ->live(debounce: 500)
                                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                            if ($state !== null && $state !== '') {
                                                                // Cuando cambio precio sin IVA, calcula el con IVA
                                                                $precioSinIva = (float) $state;
                                                                $precioConIva = round($precioSinIva * 1.13, 2);
                                                                $set('precio_con_iva_temp', $precioConIva);
                                                                // Calcula subtotal con el precio sin IVA
                                                                self::actualizarSubtotal($set, $get);
                                                            }
                                                        }),

                                                    TextInput::make('precio_con_iva_temp')
                                                        ->label('Precio + IVA (13%)')
                                                        ->numeric()
                                                        ->prefix('$')
                                                        ->step(0.01)
                                                        ->live(debounce: 500)
                                                        ->dehydrated(false)
                                                        ->default(fn(Get $get) => round((float)($get('precio_unitario') ?? 0) * 1.13, 2))
                                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                            if ($state !== null && $state !== '') {
                                                                // Cuando cambio precio con IVA, calcula el sin IVA
                                                                $precioConIva = (float) $state;
                                                                $precioSinIva = round($precioConIva / 1.13, 2);
                                                                $set('precio_unitario', $precioSinIva);
                                                                // Calcula subtotal con el precio sin IVA
                                                                self::actualizarSubtotal($set, $get);
                                                            }
                                                        }),

                                                    TextInput::make('subtotal')
                                                        ->label('Subtotal')
                                                        ->numeric()
                                                        ->readOnly()
                                                        ->prefix('$')
                                                        ->default(0)
                                                        ->extraInputAttributes(['class' => 'font-bold']),
                                                ]),

                                            Placeholder::make('info_producto_seleccionado')
                                                ->label('Información del Producto')
                                                ->content(function (Get $get) {
                                                    $productoId = $get('producto_id');
                                                    if (!$productoId) {
                                                        return new \Illuminate\Support\HtmlString('
                                                            <div class="text-center p-3 bg-gray-50 border border-gray-200 rounded">
                                                                <p class="text-sm text-gray-600">Selecciona un producto para ver su información</p>
                                                            </div>
                                                        ');
                                                    }

                                                    $producto = Producto::find($productoId);
                                                    if (!$producto) return null;

                                                    return self::generarInfoProducto($producto);
                                                })
                                                ->columnSpanFull(),
                                        ])
                                        ->defaultItems(1)
                                        ->reorderable()
                                        ->cloneable()
                                        ->collapseAllAction(fn($action) => $action->label('Contraer todos'))
                                        ->expandAllAction(fn($action) => $action->label('Expandir todos'))
                                        ->deleteAction(
                                            fn($action) => $action->requiresConfirmation(),
                                        )
                                        ->itemLabel(
                                            fn(array $state): ?string =>
                                            $state['producto_id'] ?
                                                Producto::find($state['producto_id'])?->nombre
                                                : 'Nuevo Item'
                                        )
                                        ->helperText('Agrega todos los productos para esta orden de compra')
                                        ->live()
                                        ->afterStateUpdated(fn(Get $get, Set $set) => self::calcularTotales($get, $set)),
                                ]),
                        ]),

                    Wizard\Step::make('Resumen y Totales')
                        ->icon('heroicon-o-calculator')
                        ->schema([
                            Section::make('Resumen Financiero')
                                ->description('Revisa y confirma los totales de la orden')
                                ->icon('heroicon-o-banknotes')
                                ->schema([
                                    Grid::make(2)
                                        ->schema([
                                            TextInput::make('subtotal')
                                                ->label('Subtotal')
                                                ->numeric()
                                                ->readOnly()
                                                ->prefix('$')
                                                ->default(0)
                                                ->extraInputAttributes(['class' => 'text-lg font-bold text-gray-900']),

                                            TextInput::make('impuesto_porcentaje')
                                                ->label('Impuesto (%)')
                                                ->numeric()
                                                ->default(13)
                                                ->suffix('%')
                                                ->minValue(0)
                                                ->maxValue(100)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(fn(Get $get, Set $set) => self::calcularTotales($get, $set)),

                                            TextInput::make('monto_impuesto')
                                                ->label('Monto de Impuesto')
                                                ->numeric()
                                                ->readOnly()
                                                ->prefix('$')
                                                ->default(0),

                                            TextInput::make('total')
                                                ->label('Total General')
                                                ->numeric()
                                                ->required()
                                                ->validationMessages([
                                                    'required' => 'El total no puede estar vacío.',
                                                ])
                                                ->prefix('$')
                                                ->default(0)
                                                ->extraInputAttributes(['class' => 'text-xl font-bold text-green-600']),
                                        ]),

                                    Textarea::make('observaciones')
                                        ->label('Observaciones y Notas')
                                        ->placeholder('Instrucciones especiales, términos de pago, condiciones de entrega, etc.')
                                        ->rows(3)
                                        ->columnSpanFull()
                                        ->helperText('Información adicional relevante para este pedido'),

                                    Placeholder::make('resumen')
                                        ->label('Resumen de la Orden')
                                        ->content(function (Get $get) {
                                            $items = $get('detalles') ?? [];
                                            $totalItems = count($items);
                                            $totalProductos = array_sum(array_column($items, 'cantidad') ?? []);

                                            $resumen = "Resumen: {$totalItems} tipo(s) de producto";
                                            $resumen .= ", {$totalProductos} unidad(es) en total";

                                            return $resumen;
                                        })
                                        ->extraAttributes(['class' => 'text-sm text-gray-600']),
                                ]),
                        ]),
                ])
                    ->skippable()
                    ->persistStepInQueryString()
                    ->submitAction(
                        \Filament\Forms\Components\Actions\Action::make('submit')
                            ->label('Guardar Orden de Compra')
                            ->submit('submit')
                            ->keyBindings(['mod+s'])
                    ),
            ])
            ->columns(1);
    }

    private static function generarInfoProducto($producto): \Illuminate\Support\HtmlString
    {
        $html = '<div class="p-3 bg-blue-50 border border-blue-200 rounded">';

        $html .= '
            <div class="flex items-center gap-2 mb-2">
                <span class="text-lg">�</span>
                <p class="font-semibold text-blue-800">' . $producto->nombre . '</p>
            </div>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div><span class="font-medium">Código:</span> ' . ($producto->codigo ?? 'N/A') . '</div>
                <div><span class="font-medium">Stock actual:</span> ' . $producto->stock_actual . ' ' . $producto->unidad_medida . '</div>
                <div><span class="font-medium">Stock mínimo:</span> ' . $producto->stock_minimo . '</div>
                <div><span class="font-medium">Precio compra:</span> $' . number_format($producto->precio_compra, 2) . '</div>
            </div>
        ';

        $html .= '</div>';
        return new \Illuminate\Support\HtmlString($html);
    }

    private static function actualizarSubtotal(Set $set, Get $get): void
    {
        $cantidad = (float) ($get('cantidad') ?? 0);
        $precio = (float) ($get('precio_unitario') ?? 0);
        $subtotal = $cantidad * $precio;

        $set('subtotal', number_format($subtotal, 2, '.', ''));
    }

    private static function calcularTotales(Get $get, Set $set): void
    {
        $items = $get('detalles') ?? [];
        $subtotal = 0;

        foreach ($items as $item) {
            $cantidad = (float) ($item['cantidad'] ?? 0);
            $precio = (float) ($item['precio_unitario'] ?? 0);
            $subtotal += $cantidad * $precio;
        }

        $impuestoPorcentaje = (float) ($get('impuesto_porcentaje') ?? 13);
        $montoImpuesto = $subtotal * ($impuestoPorcentaje / 100);
        $total = $subtotal + $montoImpuesto;

        $set('subtotal', number_format($subtotal, 2, '.', ''));
        $set('monto_impuesto', number_format($montoImpuesto, 2, '.', ''));
        $set('total', number_format($total, 2, '.', ''));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fecha_orden', 'desc')
            ->columns([
                TextColumn::make('numero_factura')
                    ->label('N° Orden')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->proveedor->nombre ?? '')
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->contacto_proveedor),

                TextColumn::make('fecha_orden')
                    ->label('Fecha Orden')
                    ->date('d/m/Y')
                    ->sortable()
                    ->description(fn($record) => $record->fecha_esperada?->format('d/m/Y') ?? '')
                    ->tooltip(fn($record) => 'Esperada: ' . ($record->fecha_esperada?->format('d/m/Y') ?? '')),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        if ($state === 'pendiente') return '🟡 Pendiente';
                        if ($state === 'confirmado') return '🔵 Confirmado';
                        if ($state === 'en_camino') return '🟠 En Camino';
                        if ($state === 'parcial') return '🟣 Parcial';
                        if ($state === 'completado') return '🟢 Completado';
                        if ($state === 'cancelado') return '🔴 Cancelado';
                        return $state;
                    })
                    ->color(function ($state) {
                        if ($state === 'pendiente') return 'warning';
                        if ($state === 'confirmado') return 'info';
                        if ($state === 'en_camino') return 'primary';
                        if ($state === 'parcial') return 'purple';
                        if ($state === 'completado') return 'success';
                        if ($state === 'cancelado') return 'danger';
                        return 'gray';
                    }),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->description(fn($record) => $record->detalles_count . ' items')
                    ->tooltip(fn($record) => 'Subtotal: $' . number_format($record->subtotal, 2)),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                // Filtro por estado
                SelectFilter::make('estado')
                    ->label('Estado del Pedido')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmado' => 'Confirmado',
                        'en_camino' => 'En Camino',
                        'parcial' => 'Parcialmente Recibido',
                        'completado' => 'Completado',
                        'cancelado' => 'Cancelado',
                    ])
                    ->multiple()
                    ->preload(),

                // Filtro por proveedor
                SelectFilter::make('proveedor')
                    ->relationship('proveedor', 'nombre')
                    ->searchable()
                    ->preload(),

                // Filtros mejorados por fechas
                Filter::make('fecha_orden')
                    ->form([
                        DatePicker::make('desde')->label('Desde'),
                        DatePicker::make('hasta')->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_orden', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn(Builder $query, $date): Builder => $query->whereDate('fecha_orden', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['desde'] ?? null) {
                            $indicators[] = 'Desde: ' . Carbon::parse($data['desde'])->format('d/m/Y');
                        }
                        if ($data['hasta'] ?? null) {
                            $indicators[] = 'Hasta: ' . Carbon::parse($data['hasta'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                // Filtro por mes y año
                Filter::make('mes_ano')
                    ->form([
                        Select::make('mes')->label('Mes')
                            ->options([
                                1 => 'Enero',
                                2 => 'Febrero',
                                3 => 'Marzo',
                                4 => 'Abril',
                                5 => 'Mayo',
                                6 => 'Junio',
                                7 => 'Julio',
                                8 => 'Agosto',
                                9 => 'Septiembre',
                                10 => 'Octubre',
                                11 => 'Noviembre',
                                12 => 'Diciembre'
                            ])
                            ->default(now()->month),
                        Select::make('ano')->label('Año')
                            ->options(function () {
                                $years = [];
                                $startYear = 2020;
                                $endYear = now()->year;
                                for ($year = $endYear; $year >= $startYear; $year--) {
                                    $years[$year] = $year;
                                }
                                return $years;
                            })
                            ->default(now()->year),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['mes'] ?? null,
                                fn(Builder $query, $mes): Builder => $query->whereMonth('fecha_orden', $mes)
                            )
                            ->when(
                                $data['ano'] ?? null,
                                fn(Builder $query, $ano): Builder => $query->whereYear('fecha_orden', $ano)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['mes'] ?? null) {
                            $meses = [
                                1 => 'Enero',
                                2 => 'Febrero',
                                3 => 'Marzo',
                                4 => 'Abril',
                                5 => 'Mayo',
                                6 => 'Junio',
                                7 => 'Julio',
                                8 => 'Agosto',
                                9 => 'Septiembre',
                                10 => 'Octubre',
                                11 => 'Noviembre',
                                12 => 'Diciembre'
                            ];
                            $indicators[] = 'Mes: ' . $meses[$data['mes']];
                        }
                        if ($data['ano'] ?? null) {
                            $indicators[] = 'Año: ' . $data['ano'];
                        }
                        return $indicators;
                    }),

                // Filtros de rango de montos
                Filter::make('rango_total')
                    ->form([
                        TextInput::make('min_total')->label('Monto Mínimo')->numeric()->prefix('$'),
                        TextInput::make('max_total')->label('Monto Máximo')->numeric()->prefix('$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_total'] ?? null,
                                fn(Builder $query, $min): Builder => $query->where('total', '>=', $min)
                            )
                            ->when(
                                $data['max_total'] ?? null,
                                fn(Builder $query, $max): Builder => $query->where('total', '<=', $max)
                            );
                    }),

                // Filtro: Pedidos recientes (últimos 7 días)
                Filter::make('ultimos_7_dias')
                    ->label('Últimos 7 días')
                    ->query(fn(Builder $query): Builder => $query->where('fecha_orden', '>=', now()->subDays(7)))
                    ->toggle(),

                // Filtro: Pedidos del día actual
                Filter::make('hoy')
                    ->label('Hoy')
                    ->query(fn(Builder $query): Builder => $query->whereDate('fecha_orden', today()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->color('blue'),
                    Tables\Actions\EditAction::make()->color('green'),

                    Tables\Actions\Action::make('reporte')
                        ->label('Reporte PDF')
                        ->icon('heroicon-o-document-chart-bar')
                        ->color('purple')
                        ->url(fn($record) => Pages\ReportePedido::getUrl(['record' => $record]))
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('reporte_detallado')
                        ->label('Reporte Detallado')
                        ->icon('heroicon-o-chart-bar')
                        ->color('orange')
                        ->url(fn($record) => Pages\ReportePedido::getUrl(['record' => $record])),

                    Tables\Actions\Action::make('confirmar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn($record) => $record->update(['estado' => 'confirmado']))
                        ->hidden(fn($record) => !in_array($record->estado, ['pendiente']))
                        ->requiresConfirmation()
                        ->modalHeading('Confirmar Pedido')
                        ->modalDescription('¿Estás seguro de que deseas confirmar este pedido?')
                        ->modalSubmitActionLabel('Sí, confirmar'),

                    Tables\Actions\Action::make('marcar_completado')
                        ->icon('heroicon-o-truck')
                        ->color('warning')
                        ->action(function ($record) {
                            $record->update(['estado' => 'completado']);
                            Notification::make()
                                ->title('Pedido completado')
                                ->body('El pedido ha sido marcado como completado y el stock actualizado.')
                                ->success()
                                ->send();
                        })
                        ->hidden(fn($record) => $record->estado === 'completado')
                        ->requiresConfirmation()
                        ->modalHeading('Marcar pedido como completado')
                        ->modalDescription('¿Estás seguro de que deseas marcar este pedido como completado? Esto actualizará el stock de los productos y sus variantes.')
                        ->modalSubmitActionLabel('Sí, completar pedido'),

                    Tables\Actions\Action::make('marcar_cancelado')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn($record) => $record->update(['estado' => 'cancelado']))
                        ->hidden(fn($record) => $record->estado === 'cancelado')
                        ->requiresConfirmation()
                        ->modalHeading('Cancelar Pedido')
                        ->modalDescription('¿Estás seguro de que deseas cancelar este pedido?')
                        ->modalSubmitActionLabel('Sí, cancelar'),

                    Tables\Actions\DeleteAction::make()->color('danger')->icon('heroicon-o-trash'),
                    Tables\Actions\RestoreAction::make()
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->visible(fn ($record) => method_exists($record, 'trashed') ? $record->trashed() : false),
                    Tables\Actions\ForceDeleteAction::make()
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => method_exists($record, 'trashed') ? $record->trashed() : false),
                ])->icon('heroicon-o-cog-6-tooth')->size('sm'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('generar_reporte_masivo')
                        ->label('Generar Reportes PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('primary')
                        ->action(function ($records) {
                            $pedidosIds = $records->pluck('id')->toArray();
                            return redirect()->route('pedidos.reporte.multiple', [
                                'pedidos' => $pedidosIds,
                            ]);
                        }),

                    Tables\Actions\BulkAction::make('reporte_compras_por_producto')
                        ->label('Reporte de Compras por Producto')
                        ->icon('heroicon-o-chart-pie')
                        ->color('warning')
                        ->action(function ($records) {
                            // Obtener las fechas del primer y último pedido seleccionado
                            $fechaInicio = $records->min('fecha_orden');
                            $fechaFin = $records->max('fecha_orden');
                            
                            return redirect()->route('pedidos.reporte.compras_por_producto', [
                                'fecha_inicio' => $fechaInicio?->format('Y-m-d'),
                                'fecha_fin' => $fechaFin?->format('Y-m-d'),
                            ]);
                        }),

                    Tables\Actions\BulkAction::make('marcar_completados')
                        ->label('Marcar como Completados')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['estado' => 'completado']);
                            });
                            Notification::make()
                                ->title('Pedidos completados')
                                ->body($records->count() . ' pedidos marcados como completados y stock actualizado.')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('marcar_confirmados')
                        ->label('Marcar como Confirmados')
                        ->icon('heroicon-o-check-circle')
                        ->color('info')
                        ->action(function ($records) {
                            $records->each->update(['estado' => 'confirmado']);
                            Notification::make()
                                ->title('Pedidos confirmados')
                                ->body($records->count() . ' pedidos marcados como confirmados.')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Nueva Orden de Compra'),
            ])
            ->emptyStateDescription('Comienza creando tu primera orden de compra')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
            'reporte' => Pages\ReportePedido::route('/{record}/reporte'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'proveedor',
                'detalles.producto',
                'detalles.aceite.marca',
                'detalles.aceite.tipoAceite'
            ])
            ->withCount(['detalles'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('estado', 'pendiente')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('estado', 'pendiente')->count() > 0 ? 'warning' : 'gray';
    }

    
}
