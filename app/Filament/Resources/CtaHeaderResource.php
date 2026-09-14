<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CtaHeaderResource\Pages;
use App\Filament\Resources\CtaHeaderResource\RelationManagers;
use App\Models\CtaHeader;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CtaHeaderResource extends Resource
{
    protected static ?string $model = CtaHeader::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    
    protected static ?string $navigationLabel = 'CTA Header';
    
    protected static ?string $modelLabel = 'CTA Header';
    
    protected static ?string $pluralModelLabel = 'CTA Headers';
    
    protected static ?string $navigationGroup = 'Контент';
    
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->label('Назва')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('url')
                            ->required()
                            ->label('Посилання')
                            ->maxLength(255)
                            ->helperText('Можна вказати: повний URL (http://...), маршрут з параметром (category_page:prasuvannya), ім\'я маршруту (b2b_page) або відносний шлях (/dlya-biznesu)'),
                        Forms\Components\TextInput::make('subtitle')
                            ->required()
                            ->label('Сабтайтл')
                            ->maxLength(255)
                            ->helperText('Невеликий опис під назвою'),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Порядок сортування')
                            ->numeric()
                            ->default(0)
                            ->helperText('Чим менше число, тим вище елемент'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Іконка')
                    ->schema([
                        Forms\Components\Hidden::make('icon_id')
                            ->reactive(),
                        Forms\Components\Placeholder::make('icon_selector')
                            ->label('Вибір іконки')
                            ->content(function (callable $get, callable $set) {
                                $iconId = $get('icon_id');
                                $selectedIcon = $iconId ? \App\Models\Icon::find($iconId) : null;
                                $icons = \App\Models\Icon::all();
                                
                                $previewHtml = '';
                                if ($selectedIcon && $selectedIcon->file_path) {
                                    $imageUrl = \App\Support\FilamentStorage::url($selectedIcon->file_path);
                                    $name = $selectedIcon->name ?: $selectedIcon->file_name;
                                    $previewHtml = '
                                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                            <p class="text-sm font-medium text-gray-700 mb-2">Вибрана іконка:</p>
                                            <div class="flex items-center gap-4">
                                                <img src="' . e($imageUrl) . '" alt="' . htmlspecialchars($name) . '" class="w-16 h-16 object-contain border border-gray-200 rounded p-2 bg-white">
                                                <div>
                                                    <p class="text-sm font-semibold text-gray-900">' . htmlspecialchars($name) . '</p>
                                                    ' . ($selectedIcon->file_name ? '<p class="text-xs text-gray-500">' . htmlspecialchars($selectedIcon->file_name) . '</p>' : '') . '
                                                </div>
                                            </div>
                                        </div>
                                    ';
                                }
                                
                                // Генерируем HTML для модального окна с иконками
                                $iconsGrid = '';
                                foreach ($icons as $icon) {
                                    $iconUrl = \App\Support\FilamentStorage::url($icon->file_path);
                                    $iconName = htmlspecialchars($icon->name ?: $icon->file_name);
                                    $isSelected = $iconId == $icon->id ? 'border-primary-500 bg-primary-50' : 'border-gray-200';
                                    $iconsGrid .= '
                                        <button
                                            type="button"
                                            onclick="window.selectIcon(' . $icon->id . ', \'' . e($iconUrl) . '\', \'' . addslashes($iconName) . '\')"
                                            class="p-2 border-2 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-all ' . $isSelected . '"
                                            title="' . $iconName . '"
                                        >
                                            <img 
                                                src="' . e($iconUrl) . '" 
                                                alt="' . $iconName . '"
                                                class="w-full h-full object-contain"
                                            />
                                        </button>
                                    ';
                                }
                                
                                return new \Illuminate\Support\HtmlString('
                                    <div>
                                        <button 
                                            type="button"
                                            onclick="window.openIconModal()"
                                            class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:ring ring-primary-300 disabled:opacity-25 transition ease-in-out duration-150"
                                        >
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            ' . ($selectedIcon ? 'Змінити іконку' : 'Вибрати іконку') . '
                                        </button>
                                        ' . $previewHtml . '
                                        
                                        <!-- Модальное окно выбора иконки -->
                                        <div id="icon-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="window.closeIconModal()"></div>
                                                
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                                                
                                                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                        <div class="sm:flex sm:items-start">
                                                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                                                    Виберіть іконку
                                                                </h3>
                                                                
                                                                <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-4 max-h-96 overflow-y-auto p-2">
                                                                    ' . $iconsGrid . '
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                        <button
                                                            type="button"
                                                            onclick="window.closeIconModal()"
                                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-600 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:ml-3 sm:w-auto sm:text-sm"
                                                        >
                                                            Закрити
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <script>
                                        window.openIconModal = function() {
                                            document.getElementById(\'icon-modal\').classList.remove(\'hidden\');
                                        };
                                        
                                        window.closeIconModal = function() {
                                            document.getElementById(\'icon-modal\').classList.add(\'hidden\');
                                        };
                                        
                                        window.selectIcon = function(iconId, imageUrl, iconName) {
                                            // Находим скрытое поле icon_id через различные селекторы
                                            let iconIdInput = document.querySelector(\'input[name="data.icon_id"]\');
                                            if (!iconIdInput) {
                                                iconIdInput = document.querySelector(\'input[wire\\\\:model*="icon_id"]\');
                                            }
                                            if (!iconIdInput) {
                                                iconIdInput = document.querySelector(\'input[name*="icon_id"]\');
                                            }
                                            
                                            if (iconIdInput) {
                                                // Устанавливаем значение
                                                iconIdInput.value = iconId;
                                                
                                                // Триггерим события для обновления Livewire
                                                const inputEvent = new Event(\'input\', { bubbles: true, cancelable: true });
                                                const changeEvent = new Event(\'change\', { bubbles: true, cancelable: true });
                                                
                                                iconIdInput.dispatchEvent(inputEvent);
                                                iconIdInput.dispatchEvent(changeEvent);
                                                
                                                // Для Livewire v2 - находим компонент
                                                const livewireComponent = iconIdInput.closest(\'[wire\\\\:id]\');
                                                if (livewireComponent && window.Livewire) {
                                                    const componentId = livewireComponent.getAttribute(\'wire:id\');
                                                    const component = window.Livewire.find(componentId);
                                                    if (component) {
                                                        component.set(\'data.icon_id\', iconId);
                                                    }
                                                }
                                            }
                                            
                                            // Закрываем модальное окно
                                            window.closeIconModal();
                                            
                                            // Принудительно обновляем превью через перезагрузку компонента
                                            setTimeout(() => {
                                                // Ищем Livewire компонент и обновляем его
                                                const livewireElements = document.querySelectorAll(\'[wire\\\\:id]\');
                                                livewireElements.forEach(el => {
                                                    const componentId = el.getAttribute(\'wire:id\');
                                                    if (componentId && window.Livewire) {
                                                        const component = window.Livewire.find(componentId);
                                                        if (component) {
                                                            component.call(\'$refresh\');
                                                        }
                                                    }
                                                });
                                            }, 200);
                                        };
                                        </script>
                                    </div>
                                ');
                            }),
                    ]),
                
                Forms\Components\Section::make('Статус')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Активний')
                            ->default(true)
                            ->helperText('Відображати на сайті'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon')
                    ->label('Іконка')
                    ->circular()
                    ->size(50)
                    ->getStateUsing(function ($record) {
                        if ($record->iconRelation && $record->iconRelation->file_path) {
                            return $record->iconRelation->file_path;
                        }
                        return $record->icon ?: null;
                    })
                    ->url(function ($record) {
                        if ($record->iconRelation && $record->iconRelation->file_path) {
                            return \App\Support\FilamentStorage::url($record->iconRelation->file_path);
                        }
                        if ($record->icon) {
                            return \App\Support\FilamentStorage::url($record->icon);
                        }
                        return null;
                    }),
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subtitle')
                    ->label('Сабтайтл')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('url')
                    ->label('Посилання')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активний')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активні')
                    ->placeholder('Всі')
                    ->trueLabel('Тільки активні')
                    ->falseLabel('Тільки неактивні'),
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
            'index' => Pages\ListCtaHeaders::route('/'),
            'create' => Pages\CreateCtaHeader::route('/create'),
            'edit' => Pages\EditCtaHeader::route('/{record}/edit'),
        ];
    }    
}
