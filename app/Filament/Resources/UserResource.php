<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $modelLabel = 'Usuario';
    protected static ?string $pluralModelLabel = 'Usuarios';
    protected static ?string $navigationGroup = 'Configuración del Sistema';
    protected static ?int $navigationSort = 100;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Personal')
                    ->description('Datos básicos del usuario')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Juan Carlos Pérez')
                            ->regex('/^[a-zA-Z\s]+$/')
                            ->validationMessages(
                                ['regex' => 'El nombre debe contener solo letras y espacios.']
                            )
                            ->helperText('Nombre completo del usuario'),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->regex('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')
                            ->validationMessages(
                                ['regex' => 'El correo electrónica debe ser una dirección de correo electrónica válida.']
                            )
                            ->placeholder('usuario@empresa.com')
                            ->helperText('Correo para inicio de sesión y notificaciones'),

                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Correo Verificado')
                            ->displayFormat('d/m/Y H:i')
                            ->helperText('Fecha de verificación del correo'),


                    ])
                    ->columns(2),

                Forms\Components\Section::make('Seguridad y Acceso')
                    ->description('Contraseña y roles del usuario')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn($operation) => $operation === 'create')
                            ->minLength(8)
                            ->dehydrated(fn($state) => filled($state))
                            ->regex('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/')
                            ->validationMessages(
                                ['regex' => 'La contraseña debe contener al menos 8 caracteres, una letra mayúscula, una letra minúscula, un número y un carácter especial.']
                            )
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->revealable()
                            ->helperText(fn($operation) => $operation === 'create'
                                ? 'Mínimo 8 caracteres'
                                : 'Dejar vacío para mantener la contraseña actual')
                            ->live(debounce: 500),

                        Forms\Components\Placeholder::make('password_checker')
                            ->label('Estado de Requisitos')
                            ->content(function (Get $get) {
                                $password = $get('password') ?? '';
                                
                                if (empty($password)) {
                                    return '';
                                }
                                
                                $hasLength = strlen($password) >= 8;
                                $hasLower = preg_match('/[a-z]/', $password);
                                $hasUpper = preg_match('/[A-Z]/', $password);
                                $hasNumber = preg_match('/\d/', $password);
                                $hasSpecial = preg_match('/[@$!%*?&]/', $password);
                                
                                $requirements = [
                                    ['Mínimo 8 caracteres', $hasLength],
                                    ['Letra minúscula (a-z)', $hasLower],
                                    ['Letra mayúscula (A-Z)', $hasUpper],
                                    ['Número (0-9)', $hasNumber],
                                    ['Carácter especial (@$!%*?&)', $hasSpecial],
                                ];
                                
                                $html = '<div style="margin-top: 10px; padding: 12px; background-color: #f5f5f5; border-radius: 6px; border-left: 4px solid #3b82f6;">';
                                
                                foreach ($requirements as [$label, $met]) {
                                    $color = $met ? '#10b981' : '#ef4444';
                                    $symbol = $met ? '✓' : '✗';
                                    $html .= '<div style="margin: 6px 0; color: ' . $color . '; font-weight: 500;">' . $symbol . ' ' . $label . '</div>';
                                }
                                
                                $html .= '</div>';
                                
                                return new HtmlString($html);
                            })
                            ->visible(fn(Get $get) => filled($get('password'))),

                        Forms\Components\Select::make('roles')
                            ->label('Roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->options(Role::all()->pluck('name', 'id'))
                            ->helperText('Selecciona los roles asignados al usuario')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre del Rol')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique('roles', 'name')
                                    ->placeholder('Ej: Vendedor, Administrador'),

                                Forms\Components\Select::make('guard_name')
                                    ->label('Guard')
                                    ->default('web')
                                    ->options([
                                        'web' => 'Web',
                                        'api' => 'API',
                                    ])
                                    ->required(),
                            ])
                            ->createOptionUsing(function (array $data) {
                                $role = Role::create($data);
                                return $role->id;
                            }),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->description('Datos complementarios')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('avatar_url')
                            ->label('URL del Avatar')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://ejemplo.com/avatar.jpg')
                            ->helperText('URL de la imagen de perfil'),

                        Forms\Components\KeyValue::make('custom_fields')
                            ->label('Campos Personalizados')
                            ->helperText('Información adicional en formato clave-valor'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn($record) => $record->email)
                    ->tooltip(fn($record) => $record->roles->pluck('name')->join(', ') ?: 'Sin roles'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Correo copiado al portapapeles')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->colors([
                        'primary' => 'admin',
                        'success' => 'administrador',
                        'warning' => 'vendedor',
                        'info' => 'supervisor',
                        'gray' => fn($state) => in_array($state, ['usuario', 'user']),
                    ])
                    ->searchable()
                    ->sortable()
                    ->limitList(2)
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verificado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Eliminado')
                    ->dateTime('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('verified')
                    ->label('Correo Verificado')
                    ->query(fn($query) => $query->whereNotNull('email_verified_at')),

                Tables\Filters\Filter::make('unverified')
                    ->label('Correo No Verificado')
                    ->query(fn($query) => $query->whereNull('email_verified_at')),

                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rol')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('blue')
                        ->icon('heroicon-o-eye'),

                    Tables\Actions\EditAction::make()
                        ->color('green')
                        ->icon('heroicon-o-pencil'),

                    Tables\Actions\Action::make('verify_email')
                        ->label('Verificar Email')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->action(fn($record) => $record->update(['email_verified_at' => now()]))
                        ->requiresConfirmation()
                        ->hidden(fn($record) => $record->email_verified_at !== null),

                    Tables\Actions\Action::make('impersonate')
                        ->label('Suplantar')
                        ->icon('heroicon-o-arrow-right-on-rectangle')
                        ->color('gray')
                        ->url(fn($record) => route('filament.admin.auth.impersonate', $record))
                        ->hidden(fn() => !auth()->user()->can('impersonate', User::class)),

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

                    Tables\Actions\BulkAction::make('verify_emails')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->action(fn($records) => $records->each->update(['email_verified_at' => now()])),

                    Tables\Actions\BulkAction::make('assign_role')
                        ->icon('heroicon-o-tag')
                        ->color('primary')
                        ->form([
                            Forms\Components\Select::make('role')
                                ->label('Rol a asignar')
                                ->options(Role::all()->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $role = Role::find($data['role']);
                            $records->each->assignRole($role);
                        }),
                    Tables\Actions\RestoreBulkAction::make()
                        ->label('Restaurar seleccionados')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->action(fn($records) => $records->each->restore()),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->label('Eliminar Permanentemente')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn($records) => $records->each->forceDelete()),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('Nuevo Usuario'),
            ])
            ->emptyStateHeading('Aún no hay usuarios registrados')
            ->emptyStateDescription('Comienza creando el primer usuario del sistema.')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    public static function getRelations(): array
    {
        return [
            // RelationManagers\PermissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),

        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['roles']);
    }
}
