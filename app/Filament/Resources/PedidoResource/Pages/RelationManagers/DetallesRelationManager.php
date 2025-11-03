<?php

namespace App\Filament\Resources\PedidoResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class DetallesRelationManager extends RelationManager
{
    protected static string $relationship = 'detalles';
    protected static ?string $recordTitleAttribute = 'producto_id';

    // ❌ Quita el static
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('producto_id')
                    ->label('Producto')
                    ->relationship('producto', 'nombre')
                    ->required()
                    ->searchable()
                    ->preload(),

                TextInput::make('cantidad')
                    ->label('Cantidad')
                    ->numeric()
                    ->required(),

                TextInput::make('precio_unitario')
                    ->label('Precio Unitario')
                    ->numeric()
                    ->required(),

                TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->numeric()
                    ->disabled(),
            ]);
    }

    // ❌ Quita el static
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('producto.nombre')->label('Producto')->searchable(),
                TextColumn::make('cantidad')->label('Cantidad')->sortable(),
                TextColumn::make('precio_unitario')->label('Precio Unitario')->money('USD'),
                TextColumn::make('subtotal')->label('Subtotal')->money('USD'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
