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
                                    ->validationMessages([
                                        'required' => 'El nombre es obligatorio.',
                                        'max' => 'El nombre no puede exceder 100 caracteres.',
                                    ])
                                    ->helperText('Nombres del cliente'),

                                TextInput::make('apellido')
                                    ->label('Apellidos')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('Ej: Rodríguez García')
                                    ->validationMessages([
                                        'required' => 'El apellido es obligatorio.',
                                        'max' => 'El apellido no puede exceder 100 caracteres.',
                                    ])
                                    ->helperText('Apellidos del cliente'),

                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->unique(Cliente::class, 'email', ignoreRecord: true)
                                    ->maxLength(80)
                                    ->placeholder('ejemplo@correo.com')
                                    ->validationMessages([
                                        'required' => 'El correo electrónico es obligatorio.',
                                        'email' => 'El correo debe ser una dirección válida.',
                                        'unique' => 'Este correo ya está registrado en el sistema.',
                                        'max' => 'El correo no puede exceder 80 caracteres.',
                                    ])
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
                                            ->validationMessages([
                                                'required' => 'El teléfono principal es obligatorio.',
                                                'unique' => 'Este teléfono ya está registrado.',
                                                'regex' => 'El teléfono debe tener el formato XXXX-XXXX (4 dígitos - 4 dígitos).',
                                            ])
                                            ->helperText('Teléfono de contacto'),

                                        TextInput::make('telefono_alternativo')
                                            ->label('Teléfono Alternativo')
                                            ->tel()
                                            ->mask('9999-9999')
                                            ->maxLength(9)
                                            ->validationMessages([
                                                'regex' => 'El teléfono alternativo debe tener el formato XXXX-XXXX.',
                                                'max' => 'El teléfono no puede exceder 9 caracteres.',
                                            ])
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
                            ->required(fn (\Filament\Forms\Get $get) => blank($get('nit')))
                            ->live()
                            ->afterStateUpdated(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set, ?string $state) {
                                if ($get('nit_homologado')) {
                                    $set('nit', $state);
                                }
                            })
                            ->unique(Cliente::class, 'dui', ignoreRecord: true)
                            ->minLength(9)
                            ->maxLength(9)
                            ->regex('/^\\d{9}$/', 'El DUI debe tener exactamente 9 dígitos numéricos')
                            ->mask('999999999')
                            ->numeric()
                            ->placeholder('059863879')
                            ->validationMessages([
                                'required' => 'El DUI es obligatorio si no se ingresa NIT.',
                                'unique' => 'Este DUI ya está registrado en el sistema.',
                                'min' => 'El DUI debe tener exactamente 9 dígitos.',
                                'max' => 'El DUI debe tener exactamente 9 dígitos.',
                                'regex' => 'El DUI debe contener solo 9 dígitos numéricos.',
                                'numeric' => 'El DUI solo puede contener números.',
                            ])
                            ->helperText('✓ Exactamente 9 dígitos numéricos (sin guiones)')
                            ->hint('Formato requerido: 059863879'),

                        Toggle::make('nit_homologado')
                            ->label('NIT Homologado')
                            ->inline(false)
                            ->dehydrated(false)
                            ->live()
                            ->default(function ($record) {
                                // Activar si el NIT es igual al DUI (homologado)
                                if ($record && $record->nit && $record->dui && $record->nit === $record->dui) {
                                    return true;
                                }
                                return false;
                            })
                            ->afterStateUpdated(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set, bool $state) {
                                if ($state) {
                                    $set('nit', $get('dui'));
                                } else {
                                    $set('nit', null);
                                }
                            }),

                        TextInput::make('nit')
                            ->label('NIT')
                            ->disabled(fn (\Filament\Forms\Get $get) => $get('nit_homologado'))
                            ->dehydrated()
                            ->live()
                            ->afterStateUpdated(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set, ?string $state) {
                                // Si el NIT es igual al DUI, activar el toggle de NIT Homologado
                                if ($state && $state === $get('dui')) {
                                    $set('nit_homologado', true);
                                }
                            })
                            ->unique(Cliente::class, 'nit', ignoreRecord: true)
                            ->minLength(fn (\Filament\Forms\Get $get) => $get('nit_homologado') ? 9 : 14)
                            ->maxLength(14)
                            ->regex(fn (\Filament\Forms\Get $get) => $get('nit_homologado') ? '/^\\d{9}$/' : '/^\\d{14}$/')
                            ->mask(fn (\Filament\Forms\Get $get) => $get('nit_homologado') ? '999999999' : '99999999999999')
                            ->numeric()
                            ->placeholder(fn (\Filament\Forms\Get $get) => $get('nit_homologado') ? '059863879' : '06141510901234')
                            ->validationMessages([
                                'unique' => 'Este NIT ya está registrado en el sistema.',
                                'min' => 'El NIT debe tener la longitud correcta.',
                                'max' => 'El NIT debe tener la longitud correcta.',
                                'regex' => 'El NIT debe contener solo dígitos numéricos válidos.',
                                'numeric' => 'El NIT solo puede contener números.',
                            ])
                            ->helperText(fn (\Filament\Forms\Get $get) => $get('nit_homologado') ? '✓ Copia del DUI (9 dígitos)' : '✓ Exactamente 14 dígitos numéricos (sin guiones)')
                            ->hint(fn (\Filament\Forms\Get $get) => $get('nit_homologado') ? 'Homologado con DUI' : 'Formato requerido: 06141510901234'),                                        TextInput::make('nrc')
                                            ->label('NRC')
                                            ->maxLength(20)
                                            ->placeholder('123456-7')
                                            ->validationMessages([
                                                'max' => 'El NRC no puede exceder 20 caracteres.',
                                            ])
                                            ->helperText('Número de Registro de Contribuyente (opcional)'),
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
                                    ->validationMessages([
                                        'max' => 'La razón social no puede exceder 255 caracteres.',
                                    ])
                                    ->helperText('Nombre legal de la empresa'),

                                TextInput::make('nombre_comercial')
                                    ->label('Nombre Comercial')
                                    ->maxLength(255)
                                    ->placeholder('Ej: LubriDist S.A.')
                                    ->validationMessages([
                                        'max' => 'El nombre comercial no puede exceder 255 caracteres.',
                                    ])
                                    ->helperText('Nombre comercial de la empresa'),

                                TextInput::make('giro')
                                    ->label('Giro del Negocio')
                                    ->maxLength(100)
                                    ->placeholder('Ej: Comercio al por mayor de lubricantes')
                                    ->validationMessages([
                                        'max' => 'El giro no puede exceder 100 caracteres.',
                                    ])
                                    ->helperText('Actividad principal de la empresa'),

                                TextInput::make('contacto_empresa')
                                    ->label('Persona de Contacto')
                                    ->maxLength(100)
                                    ->placeholder('Ej: Carlos Martínez - Gerente')
                                    ->validationMessages([
                                        'max' => 'El contacto no puede exceder 100 caracteres.',
                                    ])
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
                                    ->validationMessages([
                                        'required' => 'La dirección es obligatoria.',
                                        'max' => 'La dirección no puede exceder 255 caracteres.',
                                    ])
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
                                    ->live()
                                    ->required(fn ($get) => filled($get('dias_credito')) || filled($get('descuento_autorizado')))
                                    ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->validationMessages([
                                        'required' => 'El límite de crédito es obligatorio si se definen otras condiciones.',
                                        'numeric' => 'El límite de crédito debe ser un número válido.',
                                    ])
                                    ->helperText('Límite máximo de crédito autorizado'),

                                TextInput::make('dias_credito')
                                    ->label('Días de Crédito')
                                    ->numeric()
                                    ->live()
                                    ->required(fn ($get) => filled($get('limite_credito')) || filled($get('descuento_autorizado')))
                                    ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                                    ->suffix('días')
                                    ->validationMessages([
                                        'required' => 'Los días de crédito son obligatorios si se definen otras condiciones.',
                                        'numeric' => 'Los días de crédito deben ser un número válido.',
                                    ])
                                    ->helperText('Plazo de pago en días'),

                                TextInput::make('descuento_autorizado')
                                    ->label('Descuento Autorizado (%)')
                                    ->numeric()
                                    ->live()
                                    ->required(fn ($get) => filled($get('limite_credito')) || filled($get('dias_credito')))
                                    ->dehydrateStateUsing(fn ($state) => $state ?? 0)
                                    ->suffix('%')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->validationMessages([
                                        'required' => 'El descuento autorizado es obligatorio si se definen otras condiciones.',
                                        'numeric' => 'El descuento debe ser un número válido.',
                                        'min' => 'El descuento no puede ser menor a 0%.',
                                        'max' => 'El descuento no puede ser mayor a 100%.',
                                    ])
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
                            ->validationMessages([
                                'max' => 'Las observaciones no pueden exceder 1000 caracteres.',
                            ])
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
                    ->formatStateUsing(function ($state) {
                        if ($state === 'consumidor_final') return '👤 Final';
                        if ($state === 'contribuyente') return '🏢 Contribuyente';
                        if ($state === 'empresa') return '🏭 Empresa';
                        if ($state === 'distribuidor') return '🚚 Distribuidor';
                        if ($state === 'mayorista') return '📦 Mayorista';
                        return $state;
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