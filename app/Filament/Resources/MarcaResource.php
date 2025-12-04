<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MarcaResource\Pages;
use App\Models\Marca;
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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Str;

class MarcaResource extends Resource
{
    protected static ?string $model = Marca::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Catálogo';
    protected static ?string $navigationLabel = 'Marcas';
    protected static ?string $modelLabel = 'Marca';
    protected static ?string $pluralModelLabel = 'Marcas';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make('Información de la Marca')
                            ->description('Datos principales de la marca')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombre de la Marca')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Marca::class, 'nombre', ignoreRecord: true)
                                    ->placeholder('Ej. Mobil, Castrol, Shell')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $set('nombre', Str::title($state));
                                        }
                                    })
                                    ->helperText('Nombre comercial de la marca'),

                                FileUpload::make('logo')
                                    ->label('Logo de la Marca')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->helperText('Sube el logo de la marca (máx. 2MB)')
                                    ->downloadable()
                                    ->openable(),

                                TextInput::make('pais_origen')
                                    ->label('País de Origen')
                                    ->maxLength(100)
                                    ->placeholder('Ej. Estados Unidos, Reino Unido, Alemania')
                                    ->helperText('País donde se originó la marca'),

                                TextInput::make('orden')
                                    ->label('Orden de Visualización')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Número para ordenar en listas (menor = primero)'),
                            ])
                            ->columnSpan(1),

                        Section::make('Configuración y Estadísticas')
                            ->description('Estado de la marca y métricas')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Toggle::make('activo')
                                    ->label('Marca Activa')
                                    ->default(true)
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->helperText('Desactivar si la marca ya no está disponible'),

                                Placeholder::make('estadisticas')
                                    ->label('Estadísticas')
                                    ->content(function ($record) {
                                        if (!$record) return 'Nueva marca';
                                        
                                        $aceitesCount = $record->aceites()->count();
                                        $aceitesActivos = $record->aceites()->where('activo', true)->count();
                                        
                                        return "{$aceitesCount} productos totales · {$aceitesActivos} activos";
                                    })
                                    ->hidden(fn ($record) => !$record?->exists),

                                Placeholder::make('info_adicional')
                                    ->label('Información Adicional')
                                    ->content(function ($record) {
                                        if (!$record) return 'Complete los datos de la marca';
                                        
                                        return "Creada: " . $record->created_at->format('d/m/Y');
                                    })
                                    ->hidden(fn ($record) => !$record?->exists),
                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Descripción y Detalles')
                    ->description('Información adicional sobre la marca')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        Textarea::make('descripcion')
                            ->label('Descripción de la Marca')
                            ->rows(3)
                            ->placeholder('Historia de la marca, especialidades, calidad, reputación en el mercado...')
                            ->helperText('Descripción detallada de la marca y sus características'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('orden')
            ->columns([
                ImageColumn::make('logo')
                    ->label('Logo')
                    ->getStateUsing(fn ($record) => $record->logo_tipo)
                    ->circular()
                    ->size(40)
                    ->extraImgAttributes(['class' => 'object-contain'])
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->pais_origen)
                    ->tooltip(fn ($record) => $record->descripcion)
                    ->limit(30),

                TextColumn::make('pais_origen')
                    ->label('País')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
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
                    ->toggleable(isToggledHiddenByDefault: true),

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
                    ->placeholder('Todas las marcas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),

                SelectFilter::make('pais_origen')
                    ->label('País de Origen')
                    ->searchable()
                    ->preload()
                    ->options(fn () => Marca::query()
                        ->whereNotNull('pais_origen')
                        ->distinct()
                        ->pluck('pais_origen', 'pais_origen')
                        ->toArray()),

                Tables\Filters\Filter::make('con_productos')
                    ->label('Con productos')
                    ->query(fn (Builder $query): Builder => $query->has('aceites')),

                Tables\Filters\Filter::make('sin_productos')
                    ->label('Sin productos')
                    ->query(fn (Builder $query): Builder => $query->doesntHave('aceites')),

                Tables\Filters\Filter::make('recientes')
                    ->label('Agregadas este mes')
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
                        ->url(fn ($record) => AceiteResource::getUrl('index', ['tableFilters[marca][value]' => $record->id]))
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
                    ->label('Nueva Marca'),
            ])
            ->emptyStateHeading('Aún no hay marcas registradas')
            ->emptyStateDescription('Comienza registrando tu primera marca.')
            ->emptyStateIcon('heroicon-o-tag');
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\AceitesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMarcas::route('/'),
            'create' => Pages\CreateMarca::route('/create'),
            'edit' => Pages\EditMarca::route('/{record}/edit'),
           
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

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'pais_origen'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->nombre;
    }
}