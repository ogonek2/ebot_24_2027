<?php

namespace App\Filament\Resources\PromotionResource\Pages;

use App\Filament\Resources\PromotionResource;
use App\Services\PromoServiceSync;
use Filament\Resources\Pages\CreateRecord;

class CreatePromotion extends CreateRecord
{
    protected static string $resource = PromotionResource::class;

    protected function afterCreate(): void
    {
        PromoServiceSync::fromFilamentForm($this->record, $this->data);
    }
}
