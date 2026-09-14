<?php

namespace App\Filament\Resources;

use App\Filament\Resources\B2bResource\Pages;
use App\Filament\Resources\B2bResource\RelationManagers;
use App\Models\B2b;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class B2bResource extends Resource
{
    protected static ?string $model = B2b::class;

    protected static ?string $navigationIcon = 'heroicon-o-office-building';
    
    protected static ?string $navigationLabel = 'B2B';
    
    protected static ?string $modelLabel = 'B2B';
    
    protected static ?string $pluralModelLabel = 'B2B';
    
    protected static ?string $navigationGroup = 'Контент';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Назва')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->label('Заголовок')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('href')
                            ->label('URL адреса')
                            ->maxLength(255)
                            ->helperText('Буде згенеровано автоматично, якщо залишити порожнім'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Зображення')
                    ->schema([
                        Forms\Components\FileUpload::make('banner')
                            ->label('Банер')
                            ->image()
                            ->directory('sources/b2b_icons')
                            ->disk('public')
                            ->visibility('public')
                            ->nullable()
                            ->helperText('Зображення для B2B сторінки')
                            ->getUploadedFileUrlUsing(\App\Support\FilamentStorage::uploadedFileUrl()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner')
                    ->label('Банер')
                    ->circular()
                    ->size(50)
                    ->getStateUsing(function ($record) {
                        if (!$record || !$record->banner) {
                            return null;
                        }
                        // Путь в БД: sources/b2b_icons/... или src/...
                        return $record->banner;
                    })
                    ->url(function ($record) {
                        if (!$record || !$record->banner) {
                            return null;
                        }
                        // Формируем правильный URL: asset('storage/' . $record->banner)
                        // Если путь начинается с sources/, используем его как есть
                        // Если путь начинается с src/, используем его как есть
                        return asset('storage/' . $record->banner);
                    }),
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('href')
                    ->label('URL')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListB2bs::route('/'),
            'create' => Pages\CreateB2b::route('/create'),
            'edit' => Pages\EditB2b::route('/{record}/edit'),
        ];
    }    
}
