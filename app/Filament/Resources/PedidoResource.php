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
    protected static int $globalSearchResultsLimit = 5;

    protected static ?string $model = Pedido::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Compras';
    protected static ?string $navigationLabel = 'Órdenes de compra';
    protected static ?string $modelLabel = 'Orden de Compra';
    protected static ?string $pluralModelLabel = 'Órdenes de Compra';
    protected static ?int $navigationSort = 1;

    /** IVA que se usa para la conversión rápida "precio sin IVA" / "precio con IVA" de cada producto. */
    private const IVA_REFERENCIA = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Información principal')
                        ->icon('heroicon-o-document-text')
                        ->description('Proveedor y fechas')
                        ->schema([
                            Section::make('Datos de la orden')
                                ->description('Proveedor, fechas y estado de la orden de compra')
                                ->icon('heroicon-o-identification')
                                ->schema([
                                    Grid::make(['default' => 1, 'md' => 2])->schema([
                                        TextInput::make('numero_factura')
                                            ->label('Número de orden')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->placeholder('Se genera al guardar')
                                            ->prefixIcon('heroicon-o-hashtag'),

                                        Select::make('proveedor_id')
                                            ->label('Proveedor')
                                            ->relationship('proveedor', 'nombre')
                                            ->required()
                                            ->validationMessages(['required' => 'Debes seleccionar un proveedor.'])
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                $proveedor = $state ? Proveedor::find($state) : null;
                                                if ($proveedor) {
                                                    $set('contacto_proveedor', $proveedor->contacto);
                                                    $set('telefono_proveedor', $proveedor->telefono);
                                                }
                                            })
                                            ->createOptionForm([
                                                TextInput::make('nombre')->required()->maxLength(255),
                                                TextInput::make('contacto')->maxLength(255),
                                                TextInput::make('telefono')->tel()->maxLength(20),
                                            ])
                                            ->helperText('Selecciona uno existente o crea uno nuevo'),
                                    ]),

                                    Grid::make(['default' => 1, 'md' => 3])->schema([
                                        DatePicker::make('fecha_orden')
                                            ->label('Fecha de la orden')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->default(now())
                                            ->maxDate(now())
                                            ->validationMessages([
                                                'max_date' => 'La fecha de la orden no puede ser futura.',
                                                'required' => 'La fecha de la orden es obligatoria.',
                                            ]),

                                        DatePicker::make('fecha_esperada')
                                            ->label('Entrega esperada')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('d/m/Y')
                                            ->default(now()->addDays(7))
                                            ->afterOrEqual('fecha_orden')
                                            ->validationMessages([
                                                'after_or_equal' => 'La entrega esperada no puede ser anterior a la fecha de la orden.',
                                                'required' => 'La fecha esperada es obligatoria.',
                                            ]),

                                        Select::make('estado')
                                            ->label('Estado')
                                            ->options([
                                                'pendiente' => 'Pendiente',
                                                'confirmado' => 'Confirmado',
                                                'en_camino' => 'En camino',
                                                'parcial' => 'Parcialmente recibido',
                                                'completado' => 'Completado',
                                                'cancelado' => 'Cancelado',
                                            ])
                                            ->default('pendiente')
                                            ->required()
                                            ->native(false)
                                            ->validationMessages(['required' => 'El estado es obligatorio.']),
                                    ]),

                                    Grid::make(['default' => 1, 'md' => 2])->schema([
                                        TextInput::make('contacto_proveedor')
                                            ->label('Contacto en el proveedor')
                                            ->maxLength(255)
                                            ->prefixIcon('heroicon-o-user'),

                                        TextInput::make('telefono_proveedor')
                                            ->label('Teléfono de contacto')
                                            ->tel()
                                            ->maxLength(20)
                                            ->prefixIcon('heroicon-o-phone'),
                                    ]),
                                ]),

                            Section::make('Condiciones comerciales')
                                ->description('Opcional: forma de pago y condiciones de entrega')
                                ->icon('heroicon-o-clipboard-document-check')
                                ->collapsed()
                                ->schema([
                                    Grid::make(['default' => 1, 'md' => 2])->schema([
                                        Textarea::make('terminos_pago')
                                            ->label('Términos de pago')
                                            ->placeholder('Ej.: crédito a 30 días')
                                            ->rows(2),
                                        Textarea::make('condiciones_entrega')
                                            ->label('Condiciones de entrega')
                                            ->placeholder('Ej.: entrega en bodega, flete por cuenta del proveedor')
                                            ->rows(2),
                                    ]),
                                ]),
                        ]),

                    Wizard\Step::make('Productos')
                        ->icon('heroicon-o-shopping-bag')
                        ->description('Qué se compra y a qué precio')
                        ->schema([
                            Section::make('Productos de la orden')
                                ->description('Agrega cada producto con su cantidad y su precio de compra')
                                ->icon('heroicon-o-list-bullet')
                                ->schema([
                                    Forms\Components\Repeater::make('detalles')
                                        ->relationship('detalles')
                                        ->hiddenLabel()
                                        ->schema([
                                            Grid::make(['default' => 1, 'md' => 6])->schema([
                                                Select::make('producto_id')
                                                    ->label('Producto')
                                                    ->relationship('producto', 'nombre')
                                                    ->required()
                                                    ->validationMessages(['required' => 'Selecciona un producto.'])
                                                    ->searchable(['codigo', 'nombre'])
                                                    ->preload()
                                                    ->live()
                                                    ->distinct()
                                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                                    ->getSearchResultsUsing(fn (string $search) => Producto::where('codigo', 'LIKE', "%{$search}%")
                                                        ->orWhere('nombre', 'LIKE', "%{$search}%")
                                                        ->limit(50)
                                                        ->pluck('nombre', 'id'))
                                                    ->getOptionLabelUsing(fn ($value) => Producto::find($value)?->nombre ?? $value)
                                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                        $producto = $state ? Producto::find($state) : null;
                                                        if ($producto) {
                                                            $set('precio_unitario', $producto->precio_compra);
                                                            $set('precio_con_iva_temp', round($producto->precio_compra * (1 + self::IVA_REFERENCIA / 100), 2));
                                                            self::actualizarSubtotal($set, $get);
                                                        }
                                                    })
                                                    ->columnSpan(['default' => 1, 'md' => 2]),

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
                                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::actualizarSubtotal($set, $get)),

                                                TextInput::make('precio_unitario')
                                                    ->label('Precio sin IVA')
                                                    ->numeric()
                                                    ->required()
                                                    ->minValue(0)
                                                    ->prefix('$')
                                                    ->step(0.01)
                                                    ->validationMessages(['required' => 'El precio es obligatorio.'])
                                                    ->live(debounce: 500)
                                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                        if ($state !== null && $state !== '') {
                                                            $set('precio_con_iva_temp', round((float) $state * (1 + self::IVA_REFERENCIA / 100), 2));
                                                            self::actualizarSubtotal($set, $get);
                                                        }
                                                    }),

                                                TextInput::make('precio_con_iva_temp')
                                                    ->label('Precio con IVA (' . self::IVA_REFERENCIA . '%)')
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->step(0.01)
                                                    ->dehydrated(false)
                                                    ->live(debounce: 500)
                                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                        if ($state !== null && $state !== '') {
                                                            $set('precio_unitario', round((float) $state / (1 + self::IVA_REFERENCIA / 100), 2));
                                                            self::actualizarSubtotal($set, $get);
                                                        }
                                                    }),

                                                TextInput::make('subtotal')
                                                    ->label('Subtotal')
                                                    ->numeric()
                                                    ->readOnly()
                                                    ->dehydrated(false)
                                                    ->prefix('$')
                                                    ->default(0)
                                                    ->extraInputAttributes(['class' => 'font-bold']),
                                            ]),

                                            Placeholder::make('info_producto_seleccionado')
                                                ->hiddenLabel()
                                                ->content(fn (Get $get) => self::generarInfoProducto($get('producto_id') ?: null))
                                                ->columnSpanFull(),
                                        ])
                                        ->minItems(1)
                                        ->defaultItems(1)
                                        ->reorderable()
                                        ->cloneable()
                                        ->addActionLabel('Agregar producto')
                                        ->collapseAllAction(fn ($action) => $action->label('Contraer todos'))
                                        ->expandAllAction(fn ($action) => $action->label('Expandir todos'))
                                        ->deleteAction(fn ($action) => $action->requiresConfirmation())
                                        ->itemLabel(fn (array $state): ?string => ($state['producto_id'] ?? null)
                                            ? Producto::find($state['producto_id'])?->nombre
                                            : 'Nuevo producto')
                                        ->validationMessages(['min' => 'Agrega al menos un producto a la orden.'])
                                        ->live(),

                                    Placeholder::make('subtotal_productos')
                                        ->label('Subtotal de productos (sin IVA)')
                                        ->content(fn (Get $get) => '$' . number_format(self::subtotalItems($get('detalles') ?? []), 2))
                                        ->extraAttributes(['class' => 'font-bold']),
                                ]),
                        ]),

                    Wizard\Step::make('Descuento y totales')
                        ->icon('heroicon-o-calculator')
                        ->description('Descuento, IVA y total')
                        ->schema([
                            Grid::make(['default' => 1, 'lg' => 3])->schema([
                                Section::make('Descuento e impuesto')
                                    ->description('El descuento se aplica antes del IVA')
                                    ->icon('heroicon-o-receipt-percent')
                                    ->columnSpan(['default' => 1, 'lg' => 2])
                                    ->schema([
                                        Grid::make(['default' => 1, 'md' => 3])->schema([
                                            Forms\Components\ToggleButtons::make('descuento_tipo')
                                                ->label('Tipo de descuento')
                                                ->options(['monto' => 'Monto ($)', 'porcentaje' => 'Porcentaje (%)'])
                                                ->default('monto')
                                                ->inline()
                                                ->live()
                                                ->afterStateUpdated(fn (Set $set) => $set('descuento_valor', 0)),

                                            TextInput::make('descuento_valor')
                                                ->label('Descuento de la compra')
                                                ->numeric()
                                                ->default(0)
                                                ->minValue(0)
                                                ->step(0.01)
                                                ->prefix(fn (Get $get) => $get('descuento_tipo') === 'porcentaje' ? null : '$')
                                                ->suffix(fn (Get $get) => $get('descuento_tipo') === 'porcentaje' ? '%' : null)
                                                ->live(debounce: 500)
                                                ->rules([
                                                    fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                                        $value = (float) $value;
                                                        if ($get('descuento_tipo') === 'porcentaje') {
                                                            if ($value > 100) {
                                                                $fail('El porcentaje de descuento no puede ser mayor a 100.');
                                                            }
                                                            return;
                                                        }
                                                        $subtotal = round(self::subtotalItems($get('detalles') ?? []), 2);
                                                        if ($value > $subtotal) {
                                                            $fail('El descuento no puede ser mayor al subtotal ($' . number_format($subtotal, 2) . ').');
                                                        }
                                                    },
                                                ])
                                                ->helperText('Escribe 0 si la compra no tiene descuento'),

                                            TextInput::make('impuesto_porcentaje')
                                                ->label('IVA')
                                                ->numeric()
                                                ->default(self::IVA_REFERENCIA)
                                                ->suffix('%')
                                                ->minValue(0)
                                                ->maxValue(100)
                                                ->live(debounce: 500),
                                        ]),

                                        Textarea::make('observaciones')
                                            ->label('Observaciones y notas')
                                            ->placeholder('Instrucciones especiales o detalle del documento del proveedor')
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Resumen de la orden')
                                    ->icon('heroicon-o-banknotes')
                                    ->columnSpan(['default' => 1, 'lg' => 1])
                                    ->schema([
                                        Placeholder::make('resumen_totales')
                                            ->hiddenLabel()
                                            ->content(fn (Get $get) => self::resumenTotales($get)),
                                    ]),
                            ]),
                        ]),
                ])
                    ->skippable()
                    ->persistStepInQueryString(),
            ])
            ->columns(1);
    }

    /** Ficha del producto elegido en una fila (todos los textos escapados). */
    private static function generarInfoProducto(?int $productoId): \Illuminate\Support\HtmlString
    {
        $producto = $productoId ? Producto::find($productoId) : null;

        if (! $producto) {
            return new \Illuminate\Support\HtmlString('<div class="pedido-info pedido-info--vacio">Selecciona un producto para ver su existencia y su precio de compra.</div>');
        }

        $nivel = $producto->stock_actual <= 0 ? 'bad' : ($producto->stock_actual <= $producto->stock_minimo ? 'warn' : 'ok');
        $etiqueta = ['bad' => 'Agotado', 'warn' => 'Stock bajo', 'ok' => 'Stock normal'][$nivel];

        $campos = [
            'Código' => e($producto->codigo ?? 'N/A'),
            'Existencia' => e(rtrim(rtrim(number_format((float) $producto->stock_actual, 2, '.', ''), '0'), '.') . ' ' . $producto->unidad_medida) . ' <span class="pedido-chip pedido-chip--' . $nivel . '">' . $etiqueta . '</span>',
            'Mínimo' => e((string) $producto->stock_minimo),
            'Último precio de compra' => '$' . number_format((float) $producto->precio_compra, 2),
        ];

        $html = '<div class="pedido-info"><strong>' . e($producto->nombre) . '</strong><dl>';
        foreach ($campos as $k => $v) {
            $html .= '<div><dt>' . e($k) . '</dt><dd>' . $v . '</dd></div>';
        }

        return new \Illuminate\Support\HtmlString($html . '</dl></div>');
    }

    /** Tarjeta con el desglose: subtotal, descuento, base, IVA y total. */
    private static function resumenTotales(Get $get): \Illuminate\Support\HtmlString
    {
        $items = $get('detalles') ?? [];
        $t = Pedido::calcular(
            self::subtotalItems($items),
            $get('descuento_tipo'),
            (float) ($get('descuento_valor') ?? 0),
            (float) ($get('impuesto_porcentaje') ?? self::IVA_REFERENCIA),
        );

        $unidades = array_sum(array_map(fn ($i) => (float) ($i['cantidad'] ?? 0), $items));
        $detalleDescuento = $get('descuento_tipo') === 'porcentaje' && (float) $get('descuento_valor') > 0
            ? ' (' . rtrim(rtrim(number_format((float) $get('descuento_valor'), 2), '0'), '.') . '%)'
            : '';
        $m = fn (float $v) => '$' . number_format($v, 2);

        $html = '<div class="orden-resumen">'
            . '<p class="orden-resumen__items">' . count($items) . ' producto(s) · ' . rtrim(rtrim(number_format($unidades, 2), '0'), '.') . ' unidad(es)</p>'
            . '<div><span>Subtotal</span><span>' . $m($t['subtotal']) . '</span></div>'
            . '<div class="orden-resumen__desc"><span>Descuento' . e($detalleDescuento) . '</span><span>- ' . $m($t['descuento_monto']) . '</span></div>'
            . '<div><span>Base imponible</span><span>' . $m($t['base']) . '</span></div>'
            . '<div><span>IVA (' . e(rtrim(rtrim(number_format((float) ($get('impuesto_porcentaje') ?? self::IVA_REFERENCIA), 2), '0'), '.')) . '%)</span><span>' . $m($t['monto_impuesto']) . '</span></div>'
            . '<div class="orden-resumen__total"><span>Total a pagar</span><span>' . $m($t['total']) . '</span></div>'
            . '</div>';

        return new \Illuminate\Support\HtmlString($html);
    }

    private static function subtotalItems(array $items): float
    {
        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += (float) ($item['cantidad'] ?? 0) * (float) ($item['precio_unitario'] ?? 0);
        }

        return $subtotal;
    }

    private static function actualizarSubtotal(Set $set, Get $get): void
    {
        $subtotal = (float) ($get('cantidad') ?? 0) * (float) ($get('precio_unitario') ?? 0);
        $set('subtotal', number_format($subtotal, 2, '.', ''));
    }

    /**
     * Totales definitivos a partir de lo que llega del formulario (se usa al crear y al editar).
     */
    public static function totalesDesdeFormulario(array $data): array
    {
        $t = Pedido::calcular(
            self::subtotalItems($data['detalles'] ?? []),
            $data['descuento_tipo'] ?? 'monto',
            (float) ($data['descuento_valor'] ?? 0),
            (float) ($data['impuesto_porcentaje'] ?? self::IVA_REFERENCIA),
        );

        return [
            'subtotal' => $t['subtotal'],
            'descuento_tipo' => $data['descuento_tipo'] ?? 'monto',
            'descuento_valor' => (float) ($data['descuento_valor'] ?? 0),
            'descuento_monto' => $t['descuento_monto'],
            'monto_impuesto' => $t['monto_impuesto'],
            'total' => $t['total'],
        ];
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
                        if ($state === 'pendiente') return 'Pendiente';
                        if ($state === 'confirmado') return 'Confirmado';
                        if ($state === 'en_camino') return 'En Camino';
                        if ($state === 'parcial') return 'Parcial';
                        if ($state === 'completado') return 'Completado';
                        if ($state === 'cancelado') return 'Cancelado';
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
                \App\Filament\Support\Filtros::rangoFechas('fecha_orden', 'fecha_orden', 'Fecha de orden'),

                // Filtro por mes y año

                // Filtros de rango de montos
                Filter::make('rango_total')
                    ->form([
                        TextInput::make('min_total')
                            ->label('Monto Mínimo')
                            ->numeric()
                            ->prefix('$')
                            ->validationMessages([
                                'numeric' => 'El monto mínimo debe ser un número.',
                            ]),
                        TextInput::make('max_total')
                            ->label('Monto Máximo')
                            ->numeric()
                            ->prefix('$')
                            ->validationMessages([
                                'numeric' => 'El monto máximo debe ser un número.',
                            ]),
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

                // Filtro: Pedidos del día actual
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
        $pendientes = static::getModel()::where('estado', 'pendiente')->count();

        return $pendientes > 0 ? (string) $pendientes : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['numero_factura', 'proveedor.nombre'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with('proveedor');
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return 'Orden ' . ($record->numero_factura ?: '#' . $record->id);
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Proveedor' => $record->proveedor?->nombre ?? '—',
            'Estado' => ucfirst(str_replace('_', ' ', (string) $record->estado)),
        ];
    }
}
