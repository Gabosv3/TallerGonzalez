<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProveedorResource\Pages;
use App\Models\Proveedor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;

class ProveedorResource extends Resource
{
    protected static ?string $model = Proveedor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Gestión de Compras';
    protected static ?string $navigationLabel = 'Proveedores';
    protected static ?string $modelLabel = 'Proveedor';
    protected static ?string $pluralModelLabel = 'Proveedores';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make('Información Básica')
                            ->description('Datos principales del proveedor')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('codigo')
                                    ->label('Código Interno')
                                    ->maxLength(50)
                                    ->unique(Proveedor::class, 'codigo', ignoreRecord: true)
                                    ->placeholder('Ej. PROV-001')
                                    ->helperText('Código único para identificar al proveedor'),

                                TextInput::make('nombre')
                                    ->label('Razón Social')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Proveedor::class, 'nombre', ignoreRecord: true)
                                    ->placeholder('Ej. Distribuidora de Lubricantes S.A.')
                                    ->helperText('Nombre completo o razón social del proveedor'),

                                TextInput::make('contacto')
                                    ->label('Persona de Contacto')
                                    ->maxLength(255)
                                    ->placeholder('Ej. Juan Pérez - Gerente de Ventas')
                                    ->helperText('Persona responsable para contactar'),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('telefono')
                                            ->label('Teléfono')
                                            ->tel()
                                            ->maxLength(20)
                                            ->placeholder('+503 1234 5678')
                                            ->prefixIcon('heroicon-o-phone')
                                            ->helperText('Número de contacto principal'),

                                        TextInput::make('email')
                                            ->label('Correo Electrónico')
                                            ->email()
                                            ->maxLength(255)
                                            ->placeholder('contacto@proveedor.com')
                                            ->unique(Proveedor::class, 'email', ignoreRecord: true)
                                            ->prefixIcon('heroicon-o-envelope')
                                            ->helperText('Email para comunicaciones'),
                                    ]),
                            ])
                            ->columnSpan(1),

                        Section::make('Información Fiscal y Ubicación')
                            ->description('Datos para facturación y localización')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                TextInput::make('rfc')
                                    ->label('NIT')
                                    ->maxLength(50)
                                    ->placeholder('Ej. ABC123456789')
                                    ->unique(Proveedor::class, 'rfc', ignoreRecord: true)
                                    ->helperText('Registro Federal de Contribuyentes'),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('ciudad')
                                            ->label('Ciudad')
                                            ->maxLength(100)
                                            ->placeholder('Ej. San Salvador')
                                            ->helperText('Ciudad donde se ubica'),

                                        TextInput::make('pais')
                                            ->label('País')
                                            ->maxLength(100)
                                            ->default('El Salvador')
                                            ->placeholder('Ej. El Salvador')
                                            ->helperText('País del proveedor'),
                                    ]),

                                Toggle::make('activo')
                                    ->label('Proveedor Activo')
                                    ->default(true)
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->helperText('Desactivar si ya no trabaja con este proveedor'),

                                Placeholder::make('estadisticas')
                                    ->label('Estadísticas')
                                    ->content(function ($record) {
                                        if (!$record) return 'Nuevo proveedor';
                                        
                                        $pedidosCount = $record->pedidos()->count();
                                        
                                        return "{$pedidosCount} pedidos registrados";
                                    })
                                    ->hidden(fn ($record) => !$record?->exists),
                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Información Adicional')
                    ->description('Datos complementarios del proveedor')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        Textarea::make('direccion')
                            ->label('Dirección Completa')
                            ->rows(3)
                            ->placeholder('Dirección física completa para envíos y visitas')
                            ->helperText('Incluir calle, número, colonia, código postal, etc.'),

                        Textarea::make('notas')
                            ->label('Observaciones y Notas')
                            ->rows(3)
                            ->placeholder('Términos de pago, horarios de atención, condiciones especiales...')
                            ->helperText('Información adicional relevante'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('nombre')
            ->columns([
                TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->color('gray')
                    ->weight('medium'),

                TextColumn::make('nombre')
                    ->label('Razón Social')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->ciudad)
                    ->weight('medium')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->nombre),

                TextColumn::make('contacto')
                    ->label('Contacto')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->limit(25),

                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->copyMessage('Teléfono copiado al portapapeles')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->copyable()
                    ->copyMessage('Email copiado al portapapeles')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('ciudad')
                    ->label('Ciudad')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pais')
                    ->label('País')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('pedidos_count')
                    ->label('Pedidos')
                    ->counts('pedidos')
                    ->sortable()
                    ->alignCenter()
                    ->color('blue')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                TernaryFilter::make('activo')
                    ->label('Estado')
                    ->placeholder('Todos los proveedores')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),

                SelectFilter::make('ciudad')
                    ->label('Ciudad')
                    ->searchable()
                    ->preload()
                    ->options(fn () => Proveedor::query()
                        ->whereNotNull('ciudad')
                        ->distinct()
                        ->pluck('ciudad', 'ciudad')
                        ->toArray()),

                SelectFilter::make('pais')
                    ->label('País')
                    ->searchable()
                    ->preload()
                    ->options(fn () => Proveedor::query()
                        ->whereNotNull('pais')
                        ->distinct()
                        ->pluck('pais', 'pais')
                        ->toArray()),

                Tables\Filters\Filter::make('recientes')
                    ->label('Registrados este mes')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('created_at', '>=', now()->subMonth())
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('blue')
                        ->icon('heroicon-o-eye'),
                    
                    Tables\Actions\EditAction::make()
                        ->color('green')
                        ->icon('heroicon-o-pencil'),
                    
                    Tables\Actions\Action::make('contactar')
                        ->color('gray')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->url(fn ($record) => 
                            $record->email ? "mailto:{$record->email}" : null
                        )
                        ->openUrlInNewTab()
                        ->hidden(fn ($record) => empty($record->email)),
                    
                    Tables\Actions\DeleteAction::make()
                        ->color('danger')
                        ->icon('heroicon-o-trash'),
                    Tables\Actions\RestoreAction::make()
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('success')
                        ->visible(fn ($record) => method_exists($record, 'trashed') ? $record->trashed() : false),
                    Tables\Actions\ForceDeleteAction::make()
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => method_exists($record, 'trashed') ? $record->trashed() : false),
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
                        ->action(fn ($records) => $records->each->update(['activo' => true])),
                    
                    Tables\Actions\BulkAction::make('desactivar')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['activo' => false])),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Nuevo Proveedor'),
            ])
            ->emptyStateHeading('Aún no hay proveedores')
            ->emptyStateDescription('Comienza registrando tu primer proveedor.')
            ->emptyStateIcon('heroicon-o-building-storefront');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProveedors::route('/'),
            'create' => Pages\CreateProveedor::route('/create'),
            'edit' => Pages\EditProveedor::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('activo', true)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['pedidos']); // Solo contar pedidos, no productos
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'email', 'telefono', 'ciudad'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->nombre;
    }
}