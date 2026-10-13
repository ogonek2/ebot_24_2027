<?php

namespace App\Filament\Resources\ConsumerReviewResource\Pages;

use App\Filament\Resources\ConsumerReviewResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConsumerReview extends EditRecord
{
    protected static string $resource = ConsumerReviewResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
