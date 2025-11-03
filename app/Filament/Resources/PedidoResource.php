<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PedidoResource\Pages;
use App\Filament\Resources\PedidoResource\RelationManagers;
use App\Models\Pedido;
use App\Models\Proveedor;
use App\Models\Producto;
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
                                                ->required()
                                                ->unique(ignoreRecord: true)
                                                ->maxLength(50)
                                                ->placeholder('OC-'.date('Ymd').'-001')
                                                ->default('OC-'.date('Ymd').'-'.rand(100,999))
                                                ->helperText('Número único de identificación de la orden')
                                                ->hintIcon('heroicon-o-document'),

                                            Select::make('proveedor_id')
                                                ->label('Proveedor')
                                                ->relationship('proveedor', 'nombre')
                                                ->required()
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
                                                ->helperText('Fecha de emisión de la orden'),

                                            DatePicker::make('fecha_esperada')
                                                ->label('Fecha Esperada')
                                                ->required()
                                                ->minDate(now())
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
                                            Grid::make(4)
                                                ->schema([
                                                    Select::make('producto_id')
                                                        ->label('Producto')
                                                        ->relationship('producto', 'nombre')
                                                        ->required()
                                                        ->searchable()
                                                        ->preload()
                                                        ->live()
                                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                            if ($state) {
                                                                $producto = Producto::find($state);
                                                                if ($producto && $producto->precio_compra) {
                                                                    $set('precio_unitario', $producto->precio_compra);
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
                                                        ->live(onBlur: true)
                                                        ->afterStateUpdated(fn (Set $set, Get $get) => self::actualizarSubtotal($set, $get)),

                                                    TextInput::make('precio_unitario')
                                                        ->label('Precio Unitario')
                                                        ->numeric()
                                                        ->required()
                                                        ->prefix('$')
                                                        ->step(0.01)
                                                        ->live(onBlur: true)
                                                        ->afterStateUpdated(fn (Set $set, Get $get) => self::actualizarSubtotal($set, $get)),

                                                    TextInput::make('subtotal')
                                                        ->label('Subtotal')
                                                        ->numeric()
                                                        ->readOnly()
                                                        ->prefix('$')
                                                        ->default(0)
                                                        ->extraInputAttributes(['class' => 'font-bold']),
                                                ]),
                                        ])
                                        ->defaultItems(1)
                                        ->reorderable()
                                        ->cloneable()
                                        ->collapseAllAction(fn ($action) => $action->label('Contraer todos'))
                                        ->expandAllAction(fn ($action) => $action->label('Expandir todos'))
                                        ->deleteAction(
                                            fn ($action) => $action->requiresConfirmation(),
                                        )
                                        ->itemLabel(fn (array $state): ?string => 
                                            $state['producto_id'] ? Producto::find($state['producto_id'])?->nombre : 'Nuevo Item'
                                        )
                                        ->helperText('Agrega todos los productos para esta orden de compra')
                                        ->live()
                                        ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTotales($get, $set)),
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
                                            TextInput::make('subtotal_calculado')
                                                ->label('Subtotal')
                                                ->numeric()
                                                ->readOnly()
                                                ->prefix('$')
                                                ->default(0)
                                                ->extraInputAttributes(['class' => 'text-lg font-bold text-gray-900']),

                                            TextInput::make('impuesto_porcentaje')
                                                ->label('Impuesto (%)')
                                                ->numeric()
                                                ->default(16)
                                                ->suffix('%')
                                                ->minValue(0)
                                                ->maxValue(100)
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calcularTotales($get, $set)),

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
                                            
                                            return "Resumen: {$totalItems} tipo(s) de producto, {$totalProductos} unidad(es) en total";
                                        })
                                        ->extraAttributes(['class' => 'text-sm text-gray-600']),
                                ]),
                        ]),
                ])
                ->skippable()
                ->persistStepInQueryString()
                // CORRECCIÓN: Eliminado el submitAction problemático
                ->submitAction(null),
            ])
            ->columns(1);
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

        $impuestoPorcentaje = (float) ($get('impuesto_porcentaje') ?? 16);
        $montoImpuesto = $subtotal * ($impuestoPorcentaje / 100);
        $total = $subtotal + $montoImpuesto;

        $set('subtotal_calculado', number_format($subtotal, 2, '.', ''));
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
                    ->description(fn ($record) => $record->proveedor->nombre ?? '')
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('proveedor.nombre')
                    ->label('Proveedor')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->contacto_proveedor),

                TextColumn::make('fecha_orden')
                    ->label('Fecha Orden')
                    ->date('d/m/Y')
                    ->sortable()
                    ->description(fn ($record) => $record->fecha_esperada?->format('d/m/Y') ?? '')
                    ->tooltip(fn ($record) => 'Esperada: '.($record->fecha_esperada?->format('d/m/Y') ?? '')),

                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'pendiente' => '🟡 Pendiente',
                        'confirmado' => '🔵 Confirmado',
                        'en_camino' => '🟠 En Camino',
                        'parcial' => '🟣 Parcial',
                        'completado' => '🟢 Completado',
                        'cancelado' => '🔴 Cancelado',
                        default => $state
                    })
                    ->color(fn ($state) => match($state) {
                        'pendiente' => 'warning',
                        'confirmado' => 'info',
                        'en_camino' => 'primary',
                        'parcial' => 'purple',
                        'completado' => 'success',
                        'cancelado' => 'danger',
                        default => 'gray'
                    }),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->description(fn ($record) => $record->detalles_count.' items')
                    ->tooltip(fn ($record) => 'Subtotal: $'.number_format($record->subtotal, 2)),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
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

                Tables\Filters\SelectFilter::make('proveedor')
                    ->relationship('proveedor', 'nombre')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('fecha_orden')
                    ->form([
                        Forms\Components\DatePicker::make('desde'),
                        Forms\Components\DatePicker::make('hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_orden', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha_orden', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('blue'),
                    Tables\Actions\EditAction::make()
                        ->color('green'),
                    Tables\Actions\Action::make('confirmar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($record) => $record->update(['estado' => 'confirmado']))
                        ->hidden(fn ($record) => $record->estado !== 'pendiente'),
                    Tables\Actions\Action::make('marcar_completado')
                        ->icon('heroicon-o-truck')
                        ->color('warning')
                        ->action(fn ($record) => $record->update(['estado' => 'completado']))
                        ->hidden(fn ($record) => $record->estado === 'completado'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetallesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPedidos::route('/'),
            'create' => Pages\CreatePedido::route('/create'),
            'edit' => Pages\EditPedido::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['proveedor', 'detalles.producto'])
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