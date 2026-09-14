<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'Блог';

    protected static ?string $modelLabel = 'Стаття';

    protected static ?string $pluralModelLabel = 'Статті';

    protected static ?string $navigationGroup = 'Контент';

    protected static ?int $navigationSort = 6;

    protected static bool $shouldRegisterNavigation = false;

    public static function canViewAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Стаття')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Заголовок статті')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if (blank($get('slug'))) {
                                    $set('slug', \App\Models\Service::generateHref($state ?? ''));
                                }
                            })
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL (slug)')
                            ->maxLength(255)
                            ->helperText('Заповниться з заголовка; можна змінити вручну'),
                        Forms\Components\RichEditor::make('content')
                            ->label('Контент')
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'link',
                                'h1',
                                'h2',
                                'h3',
                                'codeBlock',
                                'undo',
                                'redo',
                            ])
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Зображення до статті')
                            ->image()
                            ->directory('src/blog')
                            ->disk('public')
                            ->visibility('public')
                            ->nullable()
                            ->getUploadedFileUrlUsing(\App\Support\FilamentStorage::uploadedFileUrl())
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Метатеги (SEO)')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Title')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(500),
                        Forms\Components\TextInput::make('meta_keywords')
                            ->label('Keywords')
                            ->maxLength(255),
                    ])
                    ->columns(1),
                Forms\Components\Section::make('Публікація')
                    ->schema([
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Дата та час публікації')
                            ->timezone('Europe/Kyiv')
                            ->helperText('Збережіть запис. На сайті стаття з’явиться лише після цього часу. Порожньо — чернетка (не на сайті).'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Заголовок')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Публікація')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_live')
                    ->label('На сайті')
                    ->boolean()
                    ->getStateUsing(fn (BlogPost $record): bool => $record->isPublished()),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([])
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
