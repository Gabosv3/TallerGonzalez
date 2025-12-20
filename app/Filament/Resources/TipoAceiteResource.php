<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoAceiteResource\Pages;
use App\Models\TipoAceite;
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
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Str;

class TipoAceiteResource extends Resource
{
    protected static ?string $model = TipoAceite::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Catálogo';
    protected static ?string $navigationLabel = 'Tipos de Aceite';
    protected static ?string $modelLabel = 'Tipo de Aceite';
    protected static ?string $pluralModelLabel = 'Tipos de Aceite';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make('Información del Tipo')
                            ->description('Datos básicos de clasificación')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre del Tipo')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(TipoAceite::class, 'nombre', ignoreRecord: true)
                                    ->placeholder('Ej. Sintético, Semi-Sintético, Mineral')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('nombre', Str::title($state));
                                            // Generar clave automáticamente si está vacía
                                            $clave = Str::slug($state);
                                            $set('clave', $clave);
                                        }
                                    })
                                    ->helperText('Nombre descriptivo del tipo de aceite'),

                                TextInput::make('clave')
                                    ->label('Clave Interna')
                                    ->required()
                                    ->maxLength(50)
                                    ->unique(TipoAceite::class, 'clave', ignoreRecord: true)
                                    ->placeholder('Ej. sintetico, semi-sintetico, mineral')
                                    ->helperText('Identificador único para uso interno (se genera automáticamente)'),

                                ColorPicker::make('color')
                                    ->label('Color Identificador')
                                    ->default('#6B7280')
                                    ->helperText('Color para identificar visualmente este tipo'),

                                TextInput::make('orden')
                                    ->label('Orden de Visualización')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Número para ordenar en listas (menor = primero)'),
                            ])
                            ->columnSpan(1),

                        Section::make('Configuración y Estadísticas')
                            ->description('Estado y métricas del tipo')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Toggle::make('activo')
                                    ->label('Tipo Activo')
                                    ->default(true)
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->helperText('Desactivar si este tipo ya no se usa'),

                                Placeholder::make('estadisticas')
                                    ->label('Estadísticas')
                                    ->content(function ($record) {
                                        if (!$record) return 'Nuevo tipo';
                                        
                                        $aceitesCount = $record->aceites()->count();
                                        $aceitesActivos = $record->aceites()->where('activo', true)->count();
                                        
                                        return "{$aceitesCount} productos totales · {$aceitesActivos} activos";
                                    })
                                    ->hidden(fn ($record) => !$record?->exists),

                                // CORREGIDO: Eliminado el Placeholder problemático
                                // En su lugar, usamos un simple texto informativo
                                Placeholder::make('info_color')
                                    ->label('Uso del Color')
                                    ->content('El color seleccionado se usará en badges, etiquetas y para identificación visual de este tipo de aceite.')
                                    ->hidden(fn ($get) => empty($get('color'))),
                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Descripción Detallada')
                    ->description('Información técnica y características')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        Textarea::make('descripcion')
                            ->label('Descripción del Tipo')
                            ->rows(3)
                            ->placeholder('Características técnicas, ventajas, aplicaciones recomendadas, composición...')
                            ->helperText('Descripción detallada de las características y usos de este tipo de aceite'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('orden')
            ->columns([
                ColorColumn::make('color')
                    ->label('Color')
                    ->width(50),

                TextColumn::make('nombre')
                    ->label('Nombre del Tipo')
                    ->searchable()
                    ->sortable()
                    ->weight('font-semibold'),

                TextColumn::make('clave')
                    ->label('Clave')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->color('gray'),

                TextColumn::make('aceites_count')
                    ->label('Productos')
                    ->counts('aceites')
                    ->sortable()
                    ->alignCenter()
                    ->color('blue')
                    ->description(fn ($record) => $record->aceites()->where('activo', true)->count() . ' activos'),

                TextColumn::make('orden')
                    ->label('Orden')
                    ->sortable()
                    ->alignCenter()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: false),

                IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                TernaryFilter::make('activo')
                    ->label('Estado')
                    ->placeholder('Todos los tipos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),

                Tables\Filters\Filter::make('con_productos')
                    ->label('Con productos')
                    ->query(fn (Builder $query): Builder => $query->has('aceites')),

                Tables\Filters\Filter::make('sin_productos')
                    ->label('Sin productos')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('aceites')),

                Tables\Filters\Filter::make('recientes')
                    ->label('Agregados este mes')
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
                    
                    Tables\Actions\Action::make('ver_productos')
                        ->color('gray')
                        ->icon('heroicon-o-list-bullet')
                        ->url(fn ($record) => AceiteResource::getUrl('index', ['tableFilters[tipoAceite][value]' => $record->id]))
                        ->openUrlInNewTab()
                        ->hidden(fn ($record) => $record->aceites_count == 0),
                    
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
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restaurar seleccionados')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->action(fn ($records) => $records->each->restore()),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Eliminar Permanentemente')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->forceDelete()),
                ]),
            ])
            ->reorderable('orden')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Nuevo Tipo de Aceite'),
            ])
            ->emptyStateHeading('Aún no hay tipos de aceite registrados')
            ->emptyStateDescription('Comienza registrando tu primer tipo de aceite.')
            ->emptyStateIcon('heroicon-o-rectangle-stack');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTipoAceites::route('/'),
            'create' => Pages\CreateTipoAceite::route('/create'),
            'edit' => Pages\EditTipoAceite::route('/{record}/edit'),
            
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
            ->withCount(['aceites']);
    }
}