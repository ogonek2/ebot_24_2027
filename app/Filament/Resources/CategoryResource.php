<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    
    protected static ?string $navigationLabel = 'Категорії';
    
    protected static ?string $modelLabel = 'Категорія';
    
    protected static ?string $pluralModelLabel = 'Категорії';
    
    protected static ?string $navigationGroup = 'Контент';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\Select::make('parent_id')
                            ->label('Батьківська категорія')
                            ->options(function ($record) {
                                $query = Category::query()->whereNull('parent_id');
                                // Исключаем текущую категорию из списка, чтобы избежать циклических ссылок
                                if ($record) {
                                    $query->where('id', '!=', $record->id);
                                    // Также исключаем всех потомков текущей категории
                                    $descendantIds = $record->getAllDescendants()->pluck('id')->toArray();
                                    if (!empty($descendantIds)) {
                                        $query->whereNotIn('id', $descendantIds);
                                    }
                                }
                                return $query->orderBy('sort_order')->pluck('name', 'id')->toArray();
                            })
                            ->searchable()
                            ->helperText('Оберіть батьківську категорію, якщо це вкладена категорія')
                            ->nullable(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Назва категорії')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('href')
                            ->label('URL адреса')
                            ->maxLength(255)
                            ->helperText('Буде згенеровано автоматично, якщо залишити порожнім'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Порядок сортування')
                            ->numeric()
                            ->helperText('Чим менше число, тим вище в списку. Якщо не вказано, буде автоматично встановлено максимальне значення + 1'),
                        Forms\Components\TextInput::make('category_type')
                            ->label('Тип категорії')
                            ->numeric()
                            ->default(1)
                            ->helperText('Тип категорії (для внутрішнього використання)'),
                        Forms\Components\FileUpload::make('category_img')
                            ->label('Іконка категорії')
                            ->image()
                            ->directory('src/categories_images')
                            ->disk('public')
                            ->visibility('public')
                            ->nullable()
                            ->helperText('Зображення для відображення в категорії'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Налаштування знижки')
                    ->schema([
                        Forms\Components\Toggle::make('discount_active')
                            ->label('Активувати знижку на категорію')
                            ->default(false)
                            ->reactive(),
                        
                        Forms\Components\TextInput::make('discount_percent')
                            ->label('Розмір знижки (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->suffix('%')
                            ->visible(fn ($get) => $get('discount_active'))
                            ->helperText('Від 0 до 100 відсотків'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')->label('Meta title')->maxLength(70),
                        Forms\Components\Textarea::make('meta_description')->label('Meta description')->rows(3)->maxLength(500),
                        Forms\Components\TextInput::make('meta_keywords')->label('Meta keywords')->maxLength(255),
                        Forms\Components\TextInput::make('og_title')->label('OG title')->maxLength(70),
                        Forms\Components\Textarea::make('og_description')->label('OG description')->rows(2),
                        Forms\Components\FileUpload::make('og_image')->label('OG image')->image()->directory('src/seo/categories')->disk('public'),
                        Forms\Components\TextInput::make('robots')->label('Robots')->placeholder('index, follow'),
                        Forms\Components\TextInput::make('canonical_path')->label('Canonical path')->placeholder('/poslugi/kategoriya'),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('category_img')
                    ->label('Іконка')
                    ->circular()
                    ->size(50)
                    ->getStateUsing(function ($record) {
                        if (!$record || !$record->category_img) {
                            return null;
                        }
                        // Путь в БД: src/categories_images/...
                        // Возвращаем путь относительно storage/app/public
                        return $record->category_img;
                    })
                    ->url(function ($record) {
                        if (!$record || !$record->category_img) {
                            return null;
                        }
                        // Формируем правильный URL как в Blade: asset('storage/' . $category->category_img)
                        return asset('storage/' . $record->category_img);
                    }),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        $name = $record->name;
                        if ($record->parent_id) {
                            $name = '└ ' . $name;
                        }
                        return $name;
                    }),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Батьківська категорія')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category_type')
                    ->label('Тип')
                    ->sortable(),
                Tables\Columns\TextColumn::make('href')
                    ->label('URL')
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount_percent')
                    ->label('Знижка (%)')
                    ->formatStateUsing(fn ($state) => $state ? $state . '%' : '—')
                    ->sortable(),
                Tables\Columns\IconColumn::make('discount_active')
                    ->label('Активна знижка')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('services_count')
                    ->label('Послуг')
                    ->counts('services')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_type')
                    ->label('Тип категорії')
                    ->options([
                        1 => 'Тип 1',
                        2 => 'Тип 2',
                        3 => 'Тип 3',
                    ]),
                Tables\Filters\TernaryFilter::make('discount_active')
                    ->label('Знижка')
                    ->boolean()
                    ->trueLabel('Зі знижкою')
                    ->falseLabel('Без знижки'),
            ])
            ->defaultSort('sort_order', 'asc')
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }    
}
