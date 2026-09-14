<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Models\locations;
use App\Models\cities;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class LocationResource extends Resource
{
    protected static ?string $model = locations::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    
    protected static ?string $navigationLabel = 'Приймальні пункти';
    
    protected static ?string $modelLabel = 'Приймальний пункт';
    
    protected static ?string $pluralModelLabel = 'Приймальні пункти';
    
    protected static ?string $navigationGroup = 'Налаштування';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Порядок сортування')
                            ->numeric()
                            ->helperText('Чим менше число, тим вище в списку. Якщо не вказано, буде автоматично встановлено максимальне значення + 1'),
                        Forms\Components\Textarea::make('street')
                            ->label('Адреса (вулиця)')
                            ->required()
                            ->rows(2)
                            ->maxLength(500),
                        Forms\Components\TextInput::make('district')
                            ->label('Район')
                            ->maxLength(255)
                            ->helperText('Наприклад: Печерський, Шевченківський'),
                        Forms\Components\Select::make('city')
                            ->label('Місто')
                            ->options(function () {
                                $cities = cities::all();
                                return $cities->mapWithKeys(function ($city) {
                                    return [$city->id => $city->city];
                                })->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('city')
                                    ->label('Назва міста')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return cities::create($data)->id;
                            }),
                        Forms\Components\Textarea::make('workinghourse')
                            ->label('Робочі години')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Наприклад: Пн-Пт: 9:00-18:00, Сб: 10:00-16:00'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Координати та карта')
                    ->schema([
                        Forms\Components\TextInput::make('lat')
                            ->label('Широта (Latitude)')
                            ->maxLength(255)
                            ->helperText('Координати для карти'),
                        Forms\Components\TextInput::make('lng')
                            ->label('Довгота (Longitude)')
                            ->maxLength(255)
                            ->helperText('Координати для карти'),
                        Forms\Components\TextInput::make('link_map')
                            ->label('Посилання на карту')
                            ->url()
                            ->maxLength(500)
                            ->helperText('Посилання на Google Maps або іншу карту'),
                        Forms\Components\TextInput::make('seo_link')
                            ->label('SEO посилання')
                            ->maxLength(255)
                            ->helperText('URL для SEO'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Додаткова інформація')
                    ->schema([
                        Forms\Components\FileUpload::make('banner')
                            ->label('Банер')
                            ->image()
                            ->directory('src/locations_image')
                            ->disk('public')
                            ->visibility('public')
                            ->nullable()
                            ->getUploadedFileUrlUsing(\App\Support\FilamentStorage::uploadedFileUrl()),
                        Forms\Components\Textarea::make('value')
                            ->label('Додаткова інформація')
                            ->rows(4)
                            ->maxLength(1000),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('street')
                    ->label('Адреса')
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
                Tables\Columns\TextColumn::make('district')
                    ->label('Район')
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => filled($state) ? $state : '—'),
                Tables\Columns\TextColumn::make('cityRelation.city')
                    ->label('Місто')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('workinghourse')
                    ->label('Робочі години')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 30) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\ImageColumn::make('banner')
                    ->label('Банер')
                    ->circular()
                    ->size(50)
                    ->getStateUsing(function ($record) {
                        if (!$record || !$record->banner) {
                            return null;
                        }
                        // Путь в БД: src/locations_image/...
                        return $record->banner;
                    })
                    ->url(function ($record) {
                        if (!$record || !$record->banner) {
                            return null;
                        }
                        // Формируем правильный URL: asset('storage/' . $record->banner)
                        return asset('storage/' . $record->banner);
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')
                    ->label('Місто')
                    ->options(function () {
                        $cities = cities::all();
                        return $cities->mapWithKeys(function ($city) {
                            return [$city->id => $city->city];
                        })->toArray();
                    })
                    ->query(function ($query, array $data) {
                        if (!empty($data['value'])) {
                            return $query->where('city', $data['value']);
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('sort_order', 'asc');
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}

