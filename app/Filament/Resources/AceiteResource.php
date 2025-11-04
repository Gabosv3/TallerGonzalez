<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AceiteResource\Pages;
use App\Models\Aceite;
use App\Models\Marca;
use App\Models\TipoAceite;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class AceiteResource extends Resource
{
    protected static ?string $model = Aceite::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationLabel = 'Aceites';
    protected static ?string $modelLabel = 'Aceite';
    protected static ?string $pluralModelLabel = 'Aceites';
    protected static ?string $navigationGroup = 'Inventario Automotriz';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make('Información Básica')
                            ->description('Datos principales de identificación')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Select::make('producto_id')
                                    ->label('Producto Asociado')
                                    ->relationship('producto', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Producto general al que pertenece este aceite')
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('codigo')
                                            ->maxLength(50),
                                    ]),

                                Select::make('marca_id')
                                    ->label('Marca')
                                    ->relationship('marca', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Marca del aceite')
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->required()
                                            ->maxLength(255),
                                    ]),

                                TextInput::make('modelo')
                                    ->label('Modelo/Referencia')
                                    ->maxLength(255)
                                    ->placeholder('Ej. Mobil 1 Synthetic, Castrol Magnatec')
                                    ->helperText('Modelo específico o referencia comercial'),

                                Select::make('tipo_aceite_id')
                                    ->label('Tipo de Aceite')
                                    ->relationship('tipoAceite', 'nombre')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Clasificación por composición')
                                    ->createOptionForm([
                                        TextInput::make('nombre')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('clave')
                                            ->maxLength(50),
                                    ]),
                            ])
                            ->columnSpan(1),

                        Section::make('Especificaciones Técnicas')
                            ->description('Características técnicas del aceite')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                TextInput::make('viscosidad')
                                    ->label('Viscosidad')
                                    ->required()
                                    ->maxLength(50)
                                    ->placeholder('Ej. 5W-30, 10W-40')
                                    ->helperText('Clasificación de viscosidad SAE'),

                                TextInput::make('capacidad_ml')
                                    ->label('Capacidad (ml)')
                                    ->numeric()
                                    ->required()
                                    ->step(0.01)
                                    ->suffix('ml')
                                    ->placeholder('Ej. 1000, 946, 5000')
                                    ->helperText('Capacidad en mililitros'),

                                TextInput::make('presentacion')
                                    ->label('Presentación')
                                    ->maxLength(100)
                                    ->placeholder('Ej. Botella, Bidón, Tambor')
                                    ->helperText('Tipo de envase o presentación'),

                                Placeholder::make('capacidad_formateada')
                                    ->label('Capacidad Formateada')
                                    ->content(function ($get) {
                                        $capacidad = $get('capacidad_ml');
                                        if ($capacidad && $capacidad >= 1000) {
                                            return number_format($capacidad / 1000, 2) . ' Litros';
                                        }
                                        return $capacidad ? $capacidad . ' ml' : 'No especificado';
                                    })
                                    ->hidden(fn($get) => empty($get('capacidad_ml'))),
                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Normas y Especificaciones')
                    ->description('Certificaciones y estándares de calidad')
                    ->icon('heroicon-o-shield-check')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('norma_api')
                                    ->label('Norma API')
                                    ->maxLength(50)
                                    ->placeholder('Ej. SN, CK-4, SP')
                                    ->helperText('Estándar API'),

                                TextInput::make('norma_acea')
                                    ->label('Norma ACEA')
                                    ->maxLength(50)
                                    ->placeholder('Ej. A3/B4, C3, E9')
                                    ->helperText('Estándar ACEA'),

                                TextInput::make('viscosidad_sae')
                                    ->label('Viscosidad SAE')
                                    ->maxLength(50)
                                    ->placeholder('Ej. 5W-30, 10W-40')
                                    ->helperText('Clasificación SAE completa'),
                            ]),
                    ]),

                Section::make('Características Físicas')
                    ->description('Propiedades físicas del aceite')
                    ->icon('heroicon-o-chart-bar')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('punto_ignicion')
                                    ->label('Punto de Ignición (°C)')
                                    ->numeric()
                                    ->step(0.1)
                                    ->suffix('°C')
                                    ->helperText('Temperatura de ignición'),

                                TextInput::make('punto_fluidez')
                                    ->label('Punto de Fluidez (°C)')
                                    ->numeric()
                                    ->step(0.1)
                                    ->suffix('°C')
                                    ->helperText('Temperatura de fluidez'),
                            ]),
                    ]),

                Section::make('Gestión de Inventario')
                    ->description('Control de stock y disponibilidad')
                    ->icon('heroicon-o-cube')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('stock_disponible')
                                    ->label('Stock Disponible')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->minValue(0)
                                    ->suffix('unidades')
                                    ->helperText('Cantidad actual en inventario'),

                                TextInput::make('stock_minimo')
                                    ->label('Stock Mínimo')
                                    ->numeric()
                                    ->required()
                                    ->default(5)
                                    ->minValue(0)
                                    ->suffix('unidades')
                                    ->helperText('Alerta de stock bajo'),

                                TextInput::make('stock_maximo')
                                    ->label('Stock Máximo')
                                    ->numeric()
                                    ->minValue(0)
                                    ->suffix('unidades')
                                    ->helperText('Capacidad máxima recomendada'),
                            ]),

                        Textarea::make('compatibilidad')
                            ->label('Compatibilidad')
                            ->rows(2)
                            ->placeholder('Marcas y modelos de vehículos compatibles...')
                            ->helperText('Aplicaciones recomendadas'),

                        Toggle::make('activo')
                            ->label('Producto Activo')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger')
                            ->helperText('Desactivar si el producto ya no está disponible'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('marca.nombre')
            ->columns([
                TextColumn::make('marca.nombre')
                    ->label('Marca')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn($record) => $record->modelo),

                TextColumn::make('viscosidad')
                    ->label('Viscosidad')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->tooltip(fn($record) => $record->viscosidad_sae),

                TextColumn::make('tipoAceite.nombre')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => match ($record->tipoAceite->clave ?? '') {
                        'sintetico' => 'success',
                        'semi-sintetico' => 'warning',
                        'mineral' => 'gray',
                        default => 'primary'
                    }),

                TextColumn::make('capacidad_ml')
                    ->label('Capacidad')
                    ->formatStateUsing(fn($state) => $state >= 1000 ? ($state / 1000) . ' L' : $state . ' ml')
                    ->sortable()
                    ->alignCenter()
                    ->color('gray'),

                TextColumn::make('stock_disponible')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter()
                    ->color(
                        fn($record) =>
                        $record->stock_disponible == 0 ? 'danger' : 
                        ($record->stock_disponible <= $record->stock_minimo ? 'warning' : 'success')
                    )
                    ->description(
                        fn($record) =>
                        $record->stock_minimo > 0 ? "Mín: {$record->stock_minimo}" : ''
                    ),

                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('norma_api')
                    ->label('API')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('marca')
                    ->label('Marca')
                    ->relationship('marca', 'nombre')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('tipoAceite')
                    ->label('Tipo de Aceite')
                    ->relationship('tipoAceite', 'nombre')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('viscosidad')
                    ->label('Viscosidad')
                    ->options(fn() => Aceite::query()
                        ->whereNotNull('viscosidad')
                        ->distinct()
                        ->pluck('viscosidad', 'viscosidad')
                        ->toArray())
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('activo')
                    ->label('Estado')
                    ->placeholder('Todos los productos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),

                Tables\Filters\Filter::make('bajo_stock')
                    ->label('Stock Bajo')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->whereColumn('stock_disponible', '<=', 'stock_minimo')
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('sin_stock')
                    ->label('Sin Stock')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('stock_disponible', '<=', 0)
                    )
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('blue')
                        ->icon('heroicon-o-eye'),

                    Tables\Actions\EditAction::make()
                        ->color('green')
                        ->icon('heroicon-o-pencil'),

                    Tables\Actions\Action::make('ajustar_stock')
                        ->color('orange')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->form([
                            TextInput::make('cantidad')
                                ->numeric()
                                ->required()
                                ->label('Nueva cantidad en stock'),
                        ])
                        ->action(function (Aceite $record, array $data) {
                            $record->update(['stock_disponible' => $data['cantidad']]);
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->color('danger')
                        ->icon('heroicon-o-trash'),
                ])
                    ->icon('heroicon-o-cog-6-tooth')
                    ->size('sm'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('activar')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['activo' => true])),

                    Tables\Actions\BulkAction::make('desactivar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn($records) => $records->each->update(['activo' => false])),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Nuevo Aceite'),
            ])
            ->emptyStateHeading('Aún no hay aceites registrados')
            ->emptyStateDescription('Comienza registrando tu primer aceite en el inventario.')
            ->emptyStateIcon('heroicon-o-beaker');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAceites::route('/'),
            'create' => Pages\CreateAceite::route('/create'),
            'edit' => Pages\EditAceite::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('activo', true)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $lowStockCount = static::getModel()::whereColumn('stock_disponible', '<=', 'stock_minimo')->count();
        return $lowStockCount > 0 ? 'warning' : 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['marca', 'tipoAceite', 'producto']);
    }
}