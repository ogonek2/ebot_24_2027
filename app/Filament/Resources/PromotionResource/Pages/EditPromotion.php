<?php

namespace App\Filament\Resources\PromotionResource\Pages;

use App\Filament\Resources\PromotionResource;
use App\Services\PromoServiceSync;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPromotion extends EditRecord
{
    protected static string $resource = PromotionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record;
        $record->load('services');

        $pivots = $record->services;
        $modes = $pivots->pluck('pivot.attach_mode')->unique()->values();
        $mode = $modes->count() === 1 ? $modes->first() : ($pivots->isEmpty() ? 'shared' : 'custom');

        $data['promo_attach_mode'] = $mode;

        if ($mode === 'custom') {
            $data['promo_custom_rows'] = $pivots->map(fn ($s) => [
                'service_id' => $s->id,
                'custom_percent' => $s->pivot->custom_percent,
            ])->values()->all();
            $data['promo_service_ids'] = [];
        } else {
            $data['promo_service_ids'] = $pivots->pluck('id')->all();
            $data['promo_custom_rows'] = [];
        }

        return $data;
    }

    protected function afterSave(): void
    {
        PromoServiceSync::fromFilamentForm($this->record, $this->data);
    }
}
