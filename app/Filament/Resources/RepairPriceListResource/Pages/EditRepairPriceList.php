<?php

namespace App\Filament\Resources\RepairPriceListResource\Pages;

use App\Filament\Resources\RepairPriceListResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRepairPriceList extends EditRecord
{
    protected static string $resource = RepairPriceListResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
