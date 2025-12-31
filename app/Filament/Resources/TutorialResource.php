<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TutorialResource\Pages;
use App\Models\Tutorial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;

class TutorialResource extends Resource
{
    protected static ?string $model = Tutorial::class;

    protected static ?string $navigationIcon = 'heroicon-o-play-circle';
    protected static ?string $navigationGroup = 'Ayuda y Soporte';
    protected static ?string $navigationLabel = 'Tutoriales';
    protected static ?string $modelLabel = 'Tutorial';
    protected static ?string $pluralModelLabel = 'Tutoriales';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles del Tutorial')
                    ->schema([
                        Forms\Components\TextInput::make('titulo')
                            ->label('Título')
                            ->required()
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'El título es obligatorio.',
                                'max' => 'El título no puede tener más de 255 caracteres.',
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('tipo_origen')
                            ->label('Origen del Video')
                            ->options([
                                'url' => 'URL Externa (YouTube, Vimeo, etc.)',
                                'local' => 'Subir Archivo',
                            ])
                            ->default('url')
                            ->live()
                            ->required()
                            ->validationMessages([
                                'required' => 'Debes seleccionar el origen del video.',
                            ])
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('video_path', null) && $set('video_url', null)),

                        Forms\Components\TextInput::make('video_url')
                            ->label('URL del Video')
                            ->url()
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->visible(fn (Get $get) => $get('tipo_origen') === 'url')
                            ->required(fn (Get $get) => $get('tipo_origen') === 'url')
                            ->validationMessages([
                                'required' => 'La URL del video es obligatoria cuando el origen es URL externa.',
                                'url' => 'La URL del video no es válida.',
                            ]),

                        Forms\Components\FileUpload::make('video_path')
                            ->label('Archivo de Video')
                            ->disk('public')
                            ->directory('tutoriales/videos')
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo'])
                            ->maxSize(51200) // 50MB
                            ->visible(fn (Get $get) => $get('tipo_origen') === 'local')
                            ->required(fn (Get $get) => $get('tipo_origen') === 'local')
                            ->validationMessages([
                                'required' => 'El archivo de video es obligatorio cuando el origen es local.',
                                'mimes' => 'El archivo de video debe ser un formato válido (mp4, mov, avi).',
                                'max' => 'El archivo de video no puede superar los 50 MB.',
                            ]),

                        Forms\Components\FileUpload::make('thumbnail_path')
                            ->label('Imagen Miniatura')
                            ->image()
                            ->disk('public')
                            ->directory('tutoriales/thumbnails')
                            ->validationMessages([
                                'image' => 'La miniatura debe ser una imagen válida.',
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('orden')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->validationMessages([
                                        'required' => 'El orden es obligatorio.',
                                        'numeric' => 'El orden debe ser un número.',
                                    ]),

                                Forms\Components\Toggle::make('activo')
                                    ->label('Visible')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_path')
                    ->label('Miniatura')
                    ->disk('public'),
                
                Tables\Columns\TextColumn::make('titulo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('orden')
                    ->sortable(),

                Tables\Columns\IconColumn::make('activo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('orden', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTutorials::route('/'),
            'create' => Pages\CreateTutorial::route('/create'),
            'edit' => Pages\EditTutorial::route('/{record}/edit'),
        ];
    }
}
