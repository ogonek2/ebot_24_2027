<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use App\Imports\ServicesImport;
use App\Exports\ServicesExport;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\Action;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    
    protected static ?string $navigationLabel = 'Послуги';
    
    protected static ?string $modelLabel = 'Послуга';
    
    protected static ?string $pluralModelLabel = 'Послуги';
    
    protected static ?string $navigationGroup = 'Контент';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Назва послуги')
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                // Генерируем слаг автоматически, если transform_url пустой
                                if (empty($get('transform_url'))) {
                                    $slug = Service::generateHref($state);
                                    $set('transform_url', $slug);
                                }
                            }),
                        Forms\Components\TextInput::make('transform_url')
                            ->label('URL адреса')
                            ->maxLength(255)
                            ->helperText('Буде згенеровано автоматично з назви, якщо залишити порожнім')
                            ->dehydrated(),
                        Forms\Components\RichEditor::make('description')
                            ->label('Опис')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'link',
                                'h2',
                                'h3',
                                'codeBlock',
                                'undo',
                                'redo',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Ціни')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label('Ціна (потокова)')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->suffix('₴')
                            ->helperText('Може бути числом або текстом'),
                        Forms\Components\TextInput::make('individual_price')
                            ->label('Ціна (індивідуальна)')
                            ->numeric()
                            ->minValue(0)
                            ->nullable()
                            ->suffix('₴')
                            ->helperText('Якщо не вказано, індивідуальна чистка недоступна'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Категорії та групи')
                    ->schema([
                        Forms\Components\Select::make('categories')
                            ->label('Категорії')
                            ->multiple()
                            ->options(\App\Models\Category::pluck('name', 'id'))
                            ->relationship('categories', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('groups')
                            ->label('Групи')
                            ->multiple()
                            ->options(\App\Models\Group::pluck('name', 'id'))
                            ->relationship('groups', 'name')
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Додаткова інформація')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Заголовок')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('value')
                            ->label('Значення')
                            ->rows(3)
                            ->maxLength(1000),
                        Forms\Components\TextInput::make('article')
                            ->label('Артикул')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('marker')
                            ->label('Маркер')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('seo_description')
                            ->label('SEO опис')
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\TextInput::make('seo_keywords')
                            ->label('SEO ключові слова'),
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta title')
                            ->maxLength(70),
                        Forms\Components\TextInput::make('og_title')
                            ->label('OG title')
                            ->maxLength(70),
                        Forms\Components\Textarea::make('og_description')
                            ->label('OG description')
                            ->rows(2),
                        Forms\Components\FileUpload::make('og_image')
                            ->label('OG image')
                            ->image()
                            ->directory('src/seo/services')
                            ->disk('public'),
                        Forms\Components\TextInput::make('robots')
                            ->label('Robots'),
                        Forms\Components\TextInput::make('canonical_path')
                            ->label('Canonical path'),
                        Forms\Components\Repeater::make('faq')
                            ->label('FAQ (питання та відповіді для SEO)')
                            ->schema([
                                Forms\Components\TextInput::make('question')
                                    ->label('Питання')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('answer')
                                    ->label('Відповідь')
                                    ->required()
                                    ->rows(3),
                            ])
                            ->defaultItems(0)
                            ->collapsible()
                            ->columnSpanFull()
                            ->helperText('Якщо порожньо — на сайті показуються стандартні питання для послуги.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Ціна (потокова)')
                    ->formatStateUsing(function ($state) {
                        if (is_numeric($state)) {
                            return number_format((float)$state, 0, ',', ' ') . '₴';
                        }
                        return $state ?: '—';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('individual_price')
                    ->label('Ціна (індивідуальна)')
                    ->formatStateUsing(function ($state) {
                        if (is_numeric($state) && $state > 0) {
                            return number_format((float)$state, 0, ',', ' ') . '₴';
                        }
                        return '—';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Категорії')
                    ->formatStateUsing(function ($record) {
                        $categories = $record->categories->pluck('name');
                        return $categories->isNotEmpty() ? $categories->join(', ') : '—';
                    })
                    ->limit(50),
                Tables\Columns\TextColumn::make('groups.name')
                    ->label('Групи')
                    ->formatStateUsing(function ($record) {
                        $groups = $record->groups->pluck('name');
                        return $groups->isNotEmpty() ? $groups->join(', ') : '—';
                    })
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('categories')
                    ->label('Категорія')
                    ->relationship('categories', 'name')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('groups')
                    ->label('Група')
                    ->relationship('groups', 'name')
                    ->multiple(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Експорт CSV')
                    ->icon('heroicon-s-download')
                    ->action(function () {
                        $export = new ServicesExport();
                        return $export->export();
                    }),
                Action::make('import')
                    ->label('Імпорт CSV')
                    ->icon('heroicon-s-upload')
                    ->form([
                        FileUpload::make('file')
                            ->label('CSV файл')
                            ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain', '.csv'])
                            ->disk('public')
                            ->directory('imports')
                            ->required()
                            ->storeFileNamesIn('original_filename')
                            ->visibility('private'),
                    ])
                    ->action(function (array $data) {
                        try {
                            // Логируем начало импорта
                            \Log::info('Starting import with data:', $data);
                            
                            // Получаем полный путь к файлу
                            $filePath = storage_path('app/public/' . $data['file']);
                            
                            // Проверяем существование файла
                            if (!file_exists($filePath)) {
                                // Пробуем альтернативные пути
                                $alternativePaths = [
                                    storage_path('app/public/imports/' . basename($data['file'])),
                                    storage_path('app/' . $data['file']),
                                    public_path('storage/' . $data['file']),
                                    $data['file']
                                ];
                                
                                foreach ($alternativePaths as $path) {
                                    if (file_exists($path)) {
                                        $filePath = $path;
                                        break;
                                    }
                                }
                                
                                if (!file_exists($filePath)) {
                                    throw new \Exception('Файл не найден. Проверенные пути: ' . implode(', ', array_merge([$filePath], $alternativePaths)));
                                }
                            }
                            
                            \Log::info('Using file path:', ['path' => $filePath]);
                            
                            $import = new ServicesImport();
                            $result = $import->import($filePath);
                            
                            $message = "Імпортовано: {$result['imported']} записів";
                            if (!empty($result['errors'])) {
                                $message .= "\nПомилки: " . implode("\n", $result['errors']);
                            }
                            
                            \Log::info('Import completed:', $result);
                            
                            // Показываем уведомление через Filament
                            \Filament\Notifications\Notification::make()
                                ->title('Імпорт завершено')
                                ->body($message)
                                ->success()
                                ->send();
                                
                        } catch (\Exception $e) {
                            \Log::error('Import error:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Помилка імпорту')
                                ->body('Помилка: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->successNotificationTitle('Імпорт завершено!'),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }    
}
