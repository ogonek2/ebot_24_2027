<?php

namespace App\Filament\Resources\ConsumerReviewResource\Pages;

use App\Filament\Resources\ConsumerReviewResource;
use Filament\Resources\Pages\ListRecords;

class ListConsumerReviews extends ListRecords
{
    protected static string $resource = ConsumerReviewResource::class;

    protected function getActions(): array
    {
        return [];
    }
}
