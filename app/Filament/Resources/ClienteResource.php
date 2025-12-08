<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Str;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Gestión Comercial';
    protected static ?string $navigationLabel = 'Clientes';
    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'nombre_completo';

    public static function form(Form $form): Form
    {
        // Obtener los datos del JSON
        $jsonPath = storage_path('app/public/data/departamentos-distritos-municipios-sv.json');
        $data = json_decode(file_get_contents($jsonPath), true);

        return $form
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make('Información Personal')
                            ->description('Datos básicos del cliente')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                TextInput::make('nombre')
                                    ->label('Nombres')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('Ej: María José')
                                    ->helperText('Nombres del cliente'),

                                TextInput::make('apellido')
                                    ->label('Apellidos')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('Ej: Rodríguez García')
                                    ->helperText('Apellidos del cliente'),

                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->unique(Cliente::class, 'email', ignoreRecord: true)
                                    ->maxLength(80)
                                    ->placeholder('ejemplo@correo.com')
                                    ->helperText('Correo para comunicaciones'),

                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('telefono')
                                            ->label('Teléfono Principal')
                                            ->tel()
                                            ->required()
                                            ->unique(Cliente::class, 'telefono', ignoreRecord: true)
                                            ->mask('9999-9999')
                                            ->maxLength(9)
                                            ->regex('/^\d{4}-\d{4}$/')
                                            ->placeholder('7777-8888')
                                            ->helperText('Teléfono de contacto'),

                                        TextInput::make('telefono_alternativo')
                                            ->label('Teléfono Alternativo')
                                            ->tel()
                                            ->mask('9999-9999')
                                            ->maxLength(9)
                                            ->placeholder('7777-9999')
                                            ->helperText('Teléfono adicional (opcional)'),
                                    ]),
                            ])
                            ->columnSpan(1),

                        Section::make('Documentos de Identificación')
                            ->description('Documentos para facturación en El Salvador')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                        TextInput::make('dui')
                            ->label('DUI')
                            ->required()
                            ->unique(Cliente::class, 'dui', ignoreRecord: true)
                            ->minLength(9)
                            ->maxLength(9)
                            ->regex('/^\\d{9}$/', 'El DUI debe tener exactamente 9 dígitos numéricos')
                            ->mask('999999999')
                            ->numeric()
                            ->placeholder('059863879')
                            ->helperText('✓ Exactamente 9 dígitos (sin guiones)')
                            ->hint('Formato: 059863879'),

                        TextInput::make('nit')
                            ->label('NIT')
                            ->required()
                            ->unique(Cliente::class, 'nit', ignoreRecord: true)
                            ->minLength(14)
                            ->maxLength(14)
                            ->regex('/^\\d{14}$/', 'El NIT debe tener exactamente 14 dígitos numéricos')
                            ->mask('99999999999999')
                            ->numeric()
                            ->placeholder('06141510901234')
                            ->helperText('✓ Exactamente 14 dígitos (sin guiones)')
                            ->hint('Formato: 06141510901234'),                                        TextInput::make('nrc')
                                            ->label('NRC')
                                            ->maxLength(20)
                                            ->placeholder('123456-7')
                                            ->helperText('Número de Registro de Contribuyente'),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        Select::make('tipo_cliente')
                                            ->label('Tipo de Cliente')
                                            ->options([
                                                'consumidor_final' => '👤 Consumidor Final',
                                                'contribuyente' => '🏢 Contribuyente',
                                                'empresa' => '🏭 Empresa',
                                                'distribuidor' => '🚚 Distribuidor',
                                                'mayorista' => '📦 Mayorista',
                                            ])
                                            ->default('consumidor_final')
                                            ->required()
                                            ->helperText('Seleccione el tipo para facturación'),

                                        Select::make('categoria_economica_codigo')
                                            ->label('Categoría Económica')
                                            ->options(\App\Models\CategoriaEconomica::pluck('descripcion', 'codigo'))
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Buscar categoría económica...')
                                            ->helperText('Seleccione la categoría económica'),
                                    ]),
                            ])
                            ->columnSpan(2),
                    ]),

                Section::make('Información de Empresa')
                    ->description('Datos para clientes empresariales')
                    ->icon('heroicon-o-building-office')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('razon_social')
                                    ->label('Razón Social')
                                    ->maxLength(255)
                                    ->placeholder('Ej: Distribuidora de Lubricantes S.A. de C.V.')
                                    ->helperText('Nombre legal de la empresa'),

                                TextInput::make('nombre_comercial')
                                    ->label('Nombre Comercial')
                                    ->maxLength(255)
                                    ->placeholder('Ej: LubriDist S.A.')
                                    ->helperText('Nombre comercial de la empresa'),

                                TextInput::make('giro')
                                    ->label('Giro del Negocio')
                                    ->maxLength(100)
                                    ->placeholder('Ej: Comercio al por mayor de lubricantes')
                                    ->helperText('Actividad principal de la empresa'),

                                TextInput::make('contacto_empresa')
                                    ->label('Persona de Contacto')
                                    ->maxLength(100)
                                    ->placeholder('Ej: Carlos Martínez - Gerente')
                                    ->helperText('Contacto principal en la empresa'),
                            ]),
                    ])
                    ->hidden(fn ($get) => $get('tipo_cliente') === 'consumidor_final'),

                Grid::make(2)
                    ->schema([
                        Section::make('Dirección Principal')
                            ->description('Dirección fiscal y de contacto')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Textarea::make('direccion')
                                    ->label('Dirección Completa')
                                    ->required()
                                    ->rows(3)
                                    ->maxLength(255)
                                    ->placeholder('Ej: Calle Principal #123, Colonia San Benito...')
                                    ->helperText('Dirección completa para facturación'),

                                Select::make('departamento')
                                    ->label('Departamento')
                                    ->options(function () use ($data) {
                                        return collect($data['departamentos'])->pluck('nombre', 'nombre');
                                    })
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('municipio', null))
                                    ->helperText('Seleccione el departamento'),

                                Select::make('municipio')
                                    ->label('Municipio')
                                    ->options(function (callable $get) use ($data) {
                                        $departamentoNombre = $get('departamento');
                                        if (!$departamentoNombre) return [];
                                        
                                        $departamento = collect($data['departamentos'])->firstWhere('nombre', $departamentoNombre);
                                        if (!$departamento) return [];
                                        return collect($departamento['municipios'])->pluck('nombre', 'nombre');
                                    })
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('distrito', null))
                                    ->helperText('Seleccione el municipio'),

                                Select::make('distrito')
                                    ->label('Distrito')
                                    ->options(function (callable $get) use ($data) {
                                        $departamentoNombre = $get('departamento');
                                        $municipioNombre = $get('municipio');
                                        if (!$departamentoNombre || !$municipioNombre) return [];
                                        
                                        $departamento = collect($data['departamentos'])->firstWhere('nombre', $departamentoNombre);
                                        if (!$departamento) return [];
                                        $municipio = collect($departamento['municipios'])->firstWhere('nombre', $municipioNombre);
                                        if (!$municipio) return [];
                                        return collect($municipio['distritos'])->pluck('nombre', 'nombre');
                                    })
                                    ->searchable()
                                    ->helperText('Seleccione el distrito'),

                                TextInput::make('codigo_postal')
                                    ->label('Código Postal')
                                    ->maxLength(10)
                                    ->placeholder('Ej: 01101')
                                    ->helperText('Código postal de la zona'),
                            ])
                            ->columnSpan(1),

                        Section::make('Dirección de Envío')
                            ->description('Dirección para entregas (opcional)')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Toggle::make('usar_misma_direccion')
                                    ->label('Usar misma dirección de facturación')
                                    ->default(true)
                                    ->reactive()
                                    ->helperText('Marcar si la dirección de envío es la misma'),

                                Textarea::make('envio_direccion')
                                    ->label('Dirección de Envío')
                                    ->rows(3)
                                    ->maxLength(255)
                                    ->placeholder('Ej: Calle Secundaria #456, Colonia...')
                                    ->hidden(fn ($get) => $get('usar_misma_direccion'))
                                    ->helperText('Dirección específica para entregas'),

                                TextInput::make('envio_departamento')
                                    ->label('Departamento de Envío')
                                    ->hidden(fn ($get) => $get('usar_misma_direccion'))
                                    ->helperText('Departamento para envío'),

                                TextInput::make('envio_municipio')
                                    ->label('Municipio de Envío')
                                    ->hidden(fn ($get) => $get('usar_misma_direccion'))
                                    ->helperText('Municipio para envío'),

                                TextInput::make('envio_distrito')
                                    ->label('Distrito de Envío')
                                    ->hidden(fn ($get) => $get('usar_misma_direccion'))
                                    ->helperText('Distrito para envío'),

                                Textarea::make('envio_referencia')
                                    ->label('Referencias de Envío')
                                    ->rows(2)
                                    ->maxLength(255)
                                    ->placeholder('Ej: Frente al parque, casa color azul...')
                                    ->helperText('Puntos de referencia para la entrega'),
                            ])
                            ->columnSpan(1),
                    ]),

                Section::make('Condiciones Comerciales')
                    ->description('Límites de crédito y condiciones de pago')
                    ->icon('heroicon-o-credit-card')
                    ->collapsible()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('limite_credito')
                                    ->label('Límite de Crédito ($)')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->helperText('Límite máximo de crédito autorizado'),

                                TextInput::make('dias_credito')
                                    ->label('Días de Crédito')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('días')
                                    ->helperText('Plazo de pago en días'),

                                TextInput::make('descuento_autorizado')
                                    ->label('Descuento Autorizado (%)')
                                    ->numeric()
                                    ->default(0)
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->helperText('Porcentaje de descuento autorizado'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Toggle::make('credito_activo')
                                    ->label('Crédito Activo')
                                    ->default(false)
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->helperText('Activar si el cliente tiene crédito autorizado'),

                                Toggle::make('activo')
                                    ->label('Cliente Activo')
                                    ->default(true)
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->helperText('Desactivar si el cliente ya no es cliente'),
                            ]),

                        Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(2)
                            ->placeholder('Notas adicionales sobre el cliente...')
                            ->helperText('Información relevante para el equipo comercial'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('nombre')
            ->columns([
                TextColumn::make('codigo_cliente')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->color('primary')
                    ->weight('medium')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('nombre_completo')
                    ->label('Cliente')
                    ->searchable(['nombre', 'apellido'])
                    ->sortable()
                    ->description(fn ($record) => $record->email)
                    ->tooltip(fn ($record) => $record->dui)
                    ->limit(30),

                TextColumn::make('telefono')
                    ->label('Teléfono')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->copyable()
                    ->copyMessage('Teléfono copiado'),

                BadgeColumn::make('tipo_cliente')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'consumidor_final' => '👤 Final',
                        'contribuyente' => '🏢 Contribuyente',
                        'empresa' => '🏭 Empresa',
                        'distribuidor' => '🚚 Distribuidor',
                        'mayorista' => '📦 Mayorista',
                        default => $state
                    })
                    ->colors([
                        'gray' => 'consumidor_final',
                        'blue' => 'contribuyente',
                        'green' => 'empresa',
                        'orange' => 'distribuidor',
                        'purple' => 'mayorista',
                    ])
                    ->sortable(),

                TextColumn::make('departamento')
                    ->label('Ubicación')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->description(fn ($record) => $record->municipio),

                TextColumn::make('categoriaEconomica.descripcion')
                    ->label('Categoría Económica')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->categoriaEconomica?->descripcion),

                TextColumn::make('limite_credito')
                    ->label('Límite Crédito')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn ($record) => $record->limite_credito > 0 ? 'success' : 'gray'),

                IconColumn::make('credito_activo')
                    ->label('Crédito')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-open')
                    ->falseIcon('heroicon-o-lock-closed')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),

                IconColumn::make('activo')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

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
                    ->placeholder('Todos los clientes')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),

                TernaryFilter::make('credito_activo')
                    ->label('Crédito')
                    ->placeholder('Todos')
                    ->trueLabel('Con crédito')
                    ->falseLabel('Sin crédito'),

                SelectFilter::make('tipo_cliente')
                    ->label('Tipo de Cliente')
                    ->options([
                        'consumidor_final' => 'Consumidor Final',
                        'contribuyente' => 'Contribuyente',
                        'empresa' => 'Empresa',
                        'distribuidor' => 'Distribuidor',
                        'mayorista' => 'Mayorista',
                    ])
                    ->multiple()
                    ->preload(),

                SelectFilter::make('departamento')
                    ->label('Departamento')
                    ->searchable()
                    ->preload()
                    ->options(fn () => Cliente::query()
                        ->whereNotNull('departamento')
                        ->distinct()
                        ->pluck('departamento', 'departamento')
                        ->toArray()),

                Tables\Filters\Filter::make('con_credito')
                    ->label('Con Límite de Crédito')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('limite_credito', '>', 0)
                    )
                    ->toggle(),

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
                    
                    Tables\Actions\Action::make('activar_credito')
                        ->color('success')
                        ->icon('heroicon-o-lock-open')
                        ->action(fn ($record) => $record->update(['credito_activo' => true]))
                        ->hidden(fn ($record) => $record->credito_activo),
                    
                    Tables\Actions\Action::make('desactivar_credito')
                        ->color('danger')
                        ->icon('heroicon-o-lock-closed')
                        ->action(fn ($record) => $record->update(['credito_activo' => false]))
                        ->hidden(fn ($record) => !$record->credito_activo),
                    
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
                    
                    Tables\Actions\BulkAction::make('activar_credito')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['credito_activo' => true])),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Nuevo Cliente'),
            ])
            ->emptyStateHeading('Aún no hay clientes registrados')
            ->emptyStateDescription('Comienza registrando tu primer cliente.')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\VentasRelationManager::class,
            // RelationManagers\CotizacionesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
            
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
            ->withCount([]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['nombre', 'email', 'telefono', 'dui', 'nit', 'codigo_cliente'];
    }

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->nombre;
    }
}