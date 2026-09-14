<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepairPriceListResource\Pages;
use App\Models\Category;
use App\Models\RepairPriceList;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class RepairPriceListResource extends Resource
{
    protected static ?string $model = RepairPriceList::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';

    protected static ?string $navigationLabel = 'Прайс ремонту';

    protected static ?string $modelLabel = 'Прайс ремонту';

    protected static ?string $pluralModelLabel = 'Прайси ремонту';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Основне')
                ->schema([
                    Forms\Components\Select::make('category_id')
                        ->label('Категорія')
                        ->options(fn () => Category::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('title')
                        ->label('Заголовок')
                        ->required()
                        ->default('Прайс на ремонт')
                        ->maxLength(255),
                    Forms\Components\Toggle::make('is_published')
                        ->label('Опубліковано')
                        ->default(true),
                ])
                ->columns(3),

            Forms\Components\Section::make('Секції та позиції')
                ->schema([
                    Forms\Components\Repeater::make('sections')
                        ->relationship()
                        ->label('Секції')
                        ->orderable('sort_order')
                        ->defaultItems(0)
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Секція')
                        ->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('Назва секції')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('sort_order')
                                ->label('Порядок')
                                ->numeric()
                                ->default(0),
                            Forms\Components\Repeater::make('items')
                                ->relationship()
                                ->label('Позиції')
                                ->orderable('sort_order')
                                ->defaultItems(1)
                                ->collapsible()
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Назва')
                                        ->required()
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('price')
                                        ->label('Ціна')
                                        ->numeric()
                                        ->required()
                                        ->suffix('₴'),
                                    Forms\Components\TextInput::make('price_prefix')
                                        ->label('Префікс')
                                        ->default('від')
                                        ->maxLength(32),
                                    Forms\Components\TextInput::make('unit')
                                        ->label('Одиниця')
                                        ->placeholder('за одиницю')
                                        ->maxLength(64),
                                    Forms\Components\TextInput::make('note')
                                        ->label('Примітка')
                                        ->maxLength(255)
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('sort_order')
                                        ->label('Порядок')
                                        ->numeric()
                                        ->default(0),
                                ])
                                ->columns(4)
                                ->columnSpanFull(),
                        ])
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('Заголовок')->searchable(),
                Tables\Columns\TextColumn::make('category.name')->label('Категорія')->searchable(),
                Tables\Columns\IconColumn::make('is_published')->label('Опубл.')->boolean(),
                Tables\Columns\TextColumn::make('sections_count')
                    ->counts('sections')
                    ->label('Секцій'),
                Tables\Columns\TextColumn::make('updated_at')->label('Оновлено')->dateTime('d.m.Y H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRepairPriceLists::route('/'),
            'create' => Pages\CreateRepairPriceList::route('/create'),
            'edit' => Pages\EditRepairPriceList::route('/{record}/edit'),
        ];
    }
}
