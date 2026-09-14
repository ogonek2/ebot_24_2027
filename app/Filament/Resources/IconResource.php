<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IconResource\Pages;
use App\Filament\Resources\IconResource\RelationManagers;
use App\Models\Icon;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IconResource extends Resource
{
    protected static ?string $model = Icon::class;

    protected static ?string $slug = 'icons';

    protected static ?string $navigationIcon = 'heroicon-o-photograph';
    
    protected static ?string $navigationLabel = 'Іконки';
    
    protected static ?string $modelLabel = 'Іконка';
    
    protected static ?string $pluralModelLabel = 'Іконки';
    
    protected static ?string $navigationGroup = 'Контент';
    
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Назва іконки')
                            ->maxLength(255)
                            ->helperText('Необов\'язкова назва для ідентифікації іконки'),
                    ]),
                
                Forms\Components\Section::make('Файл іконки')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Файл іконки')
                            ->directory('src/icons_svg')
                            ->disk('public')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/svg+xml', 'image/svg', 'image/png', 'image/jpeg', 'image/jpg'])
                            ->maxSize(2048)
                            ->required()
                            ->helperText('SVG, PNG або JPEG. Максимальний розмір: 2MB')
                            ->storeFileNamesIn('file_name')
                            ->imagePreviewHeight('150')
                            ->getUploadedFileUrlUsing(\App\Support\FilamentStorage::uploadedFileUrl()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('file_path')
                    ->label('Іконка')
                    ->getStateUsing(fn ($record) => $record->file_path)
                    ->url(fn ($record) => \App\Support\FilamentStorage::url($record->file_path))
                    ->size(50)
                    ->circular(false),
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_name')
                    ->label('Ім\'я файлу')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('mime_type')
                    ->label('Тип')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('file_size')
                    ->label('Розмір')
                    ->formatStateUsing(function ($state) {
                        return $state ? number_format($state / 1024, 2) . ' KB' : '—';
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
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
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListIcons::route('/'),
            'create' => Pages\CreateIcon::route('/create'),
            'edit' => Pages\EditIcon::route('/{record}/edit'),
            'bulk-upload' => Pages\BulkUploadIcons::route('/bulk-upload'),
        ];
    }    
}
