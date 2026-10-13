<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsumerReviewResource\Pages;
use App\Models\ConsumerReview;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ConsumerReviewResource extends Resource
{
    protected static ?string $model = ConsumerReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-alt-2';

    protected static ?string $navigationLabel = 'Куточок споживача';

    protected static ?string $modelLabel = 'Звернення';

    protected static ?string $pluralModelLabel = 'Звернення клієнтів';

    protected static ?string $navigationGroup = 'Замовлення';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Клієнт')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ім\'я')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Телефон')
                            ->required()
                            ->tel()
                            ->maxLength(40),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('order_number')
                            ->label('Номер замовлення')
                            ->maxLength(100),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Звернення')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Тип')
                            ->options(ConsumerReview::getTypes())
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options(ConsumerReview::getStatuses())
                            ->required(),
                        Forms\Components\Textarea::make('message')
                            ->label('Текст')
                            ->required()
                            ->rows(6)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Нотатки адміністратора')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('created_at_info')
                            ->label('Створено')
                            ->content(fn ($record) => $record?->created_at?->format('d.m.Y H:i:s') ?? '—'),
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
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => ConsumerReview::getTypes()[$state] ?? $state)
                    ->colors([
                        'primary' => 'review',
                        'warning' => 'claim',
                        'success' => 'suggestion',
                    ]),
                Tables\Columns\TextColumn::make('name')
                    ->label('Ім\'я')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Замовлення')
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn ($state) => ConsumerReview::getStatuses()[$state] ?? $state)
                    ->colors([
                        'danger' => 'new',
                        'warning' => 'in_progress',
                        'success' => 'done',
                        'secondary' => 'archived',
                    ]),
                Tables\Columns\TextColumn::make('message')
                    ->label('Текст')
                    ->limit(40)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Дата')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Статус')
                    ->options(ConsumerReview::getStatuses()),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Тип')
                    ->options(ConsumerReview::getTypes()),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConsumerReviews::route('/'),
            'edit' => Pages\EditConsumerReview::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
