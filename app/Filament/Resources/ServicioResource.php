<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServicioResource\Pages;
use App\Models\Servicio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServicioResource extends Resource
{
    protected static ?string $model = Servicio::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Mantenimiento';
    protected static ?string $navigationLabel = 'Servicios';
    protected static ?string $modelLabel = 'Servicio';
    protected static ?string $pluralModelLabel = 'Servicios';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'placa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Servicio')
                    ->description('Datos básicos del servicio de vehículo')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('placa')
                                    ->label('Placa del Vehículo')
                                    ->required()
                                    ->maxLength(10)
                                    ->placeholder('ABC-123')
                                    ->validationMessages([
                                        'required' => 'La placa es obligatoria.',
                                        'max' => 'La placa no puede exceder 10 caracteres.',
                                    ])
                                    ->helperText('Placa única del vehículo'),

                                Forms\Components\Select::make('estado')
                                    ->label('Estado del Servicio')
                                    ->options([
                                        'pendiente' => '🟡 Pendiente',
                                        'en_proceso' => '🔵 En Proceso',
                                        'completado' => '🟢 Completado',
                                        'cancelado' => '🔴 Cancelado',
                                    ])
                                    ->default('pendiente')
                                    ->required()
                                    ->helperText('Selecciona el estado actual del servicio'),
                            ]),

                        Forms\Components\Textarea::make('notas')
                            ->label('Notas del Servicio')
                            ->placeholder('Observaciones o detalles importantes del servicio...')
                            ->rows(3)
                            ->maxLength(500)
                            ->validationMessages([
                                'max' => 'Las notas no pueden exceder 500 caracteres.',
                            ])
                            ->helperText('Información adicional del servicio'),
                    ]),

                Forms\Components\Section::make('Productos Utilizados')
                    ->description('Registra los productos usados en el servicio')
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        Forms\Components\Repeater::make('productos')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('producto')
                                            ->label('Nombre del Producto')
                                            ->required()
                                            ->maxLength(100)
                                            ->placeholder('Ej: Aceite 5W30, Filtro, etc.')
                                            ->validationMessages([
                                                'required' => 'El nombre del producto es obligatorio.',
                                                'max' => 'El nombre no puede exceder 100 caracteres.',
                                            ])
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('cantidad')
                                            ->label('Cantidad')
                                            ->numeric()
                                            ->required()
                                            ->default(1)
                                            ->minValue(0.01)
                                            ->step(0.01)
                                            ->validationMessages([
                                                'required' => 'La cantidad es obligatoria.',
                                                'numeric' => 'La cantidad debe ser un número válido.',
                                                'min' => 'La cantidad debe ser mayor a 0.',
                                            ])
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->defaultItems(1)
                            ->minItems(1)
                            ->reorderable()
                            ->cloneable()
                            ->collapsible()
                            ->collapseAllAction(fn($action) => $action->label('Contraer todos'))
                            ->expandAllAction(fn($action) => $action->label('Expandir todos'))
                            ->deleteAction(
                                fn($action) => $action->requiresConfirmation(),
                            )
                            ->itemLabel(
                                fn(array $state): ?string =>
                                isset($state['producto']) ? $state['producto'] . ' (Qty: ' . ($state['cantidad'] ?? 0) . ')' : 'Nuevo Producto'
                            )
                            ->helperText('Agrega todos los productos utilizados en este servicio')
                            ->live(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('placa')
                    ->label('Placa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->limit(15),

                Tables\Columns\TextColumn::make('cantidad_productos')
                    ->label('Productos')
                    ->getStateUsing(fn(Servicio $record) => count($record->productos ?? []))
                    ->description(fn(Servicio $record) => 'Total: ' . $record->total_unidades . ' unidades')
                    ->sortable()
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => match($state) {
                        'pendiente' => '🟡 Pendiente',
                        'en_proceso' => '🔵 En Proceso',
                        'completado' => '🟢 Completado',
                        'cancelado' => '🔴 Cancelado',
                        default => $state
                    })
                    ->colors([
                        'warning' => 'pendiente',
                        'info' => 'en_proceso',
                        'success' => 'completado',
                        'danger' => 'cancelado',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => '🟡 Pendiente',
                        'en_proceso' => '🔵 En Proceso',
                        'completado' => '🟢 Completado',
                        'cancelado' => '🔴 Cancelado',
                    ])
                    ->multiple()
                    ->preload(),

                Tables\Filters\Filter::make('recientes')
                    ->label('Últimos 7 días')
                    ->query(fn(Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(7))),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('blue')
                        ->icon('heroicon-o-eye'),
                    
                    Tables\Actions\EditAction::make()
                        ->color('green')
                        ->icon('heroicon-o-pencil'),

                    Tables\Actions\Action::make('cambiar_estado_proceso')
                        ->label('Marcar en Proceso')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->action(fn(Servicio $record) => $record->update(['estado' => 'en_proceso']))
                        ->hidden(fn(Servicio $record) => $record->estado !== 'pendiente')
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('cambiar_estado_completado')
                        ->label('Completar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn(Servicio $record) => $record->update(['estado' => 'completado']))
                        ->hidden(fn(Servicio $record) => in_array($record->estado, ['completado', 'cancelado']))
                        ->requiresConfirmation(),

                    Tables\Actions\Action::make('cambiar_estado_cancelado')
                        ->label('Cancelar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn(Servicio $record) => $record->update(['estado' => 'cancelado']))
                        ->hidden(fn(Servicio $record) => $record->estado === 'cancelado')
                        ->requiresConfirmation(),
                    
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

                    Tables\Actions\BulkAction::make('cambiar_estado_bulk')
                        ->label('Cambiar estado a')
                        ->icon('heroicon-o-arrow-path')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('estado')
                                ->label('Nuevo estado')
                                ->options([
                                    'pendiente' => '🟡 Pendiente',
                                    'en_proceso' => '🔵 En Proceso',
                                    'completado' => '🟢 Completado',
                                    'cancelado' => '🔴 Cancelado',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update(['estado' => $data['estado']]);
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Nuevo Servicio'),
            ])
            ->emptyStateHeading('No hay servicios registrados')
            ->emptyStateDescription('Comienza registrando el primer servicio de vehículo.')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServicios::route('/'),
            'create' => Pages\CreateServicio::route('/create'),
            'view' => Pages\ViewServicio::route('/{record}'),
            'edit' => Pages\EditServicio::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('estado', 'pendiente')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['placa'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->placa;
    }
}
