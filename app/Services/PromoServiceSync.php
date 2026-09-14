<?php

namespace App\Services;

use App\Models\discount;

class PromoServiceSync
{
    /**
     * @param  array<string, mixed>  $state
     */
    public static function fromFilamentForm(discount $record, array $state): void
    {
        $mode = $state['promo_attach_mode'] ?? 'shared';
        if (!in_array($mode, ['shared', 'custom', 'none'], true)) {
            $mode = 'shared';
        }

        $rows = [];
        if ($mode === 'custom') {
            foreach ($state['promo_custom_rows'] ?? [] as $row) {
                if (empty($row['service_id'])) {
                    continue;
                }
                $rows[] = [
                    'service_id' => (int) $row['service_id'],
                    'attach_mode' => 'custom',
                    'custom_percent' => isset($row['custom_percent']) ? (int) $row['custom_percent'] : null,
                ];
            }
        } else {
            foreach ($state['promo_service_ids'] ?? [] as $serviceId) {
                $rows[] = [
                    'service_id' => (int) $serviceId,
                    'attach_mode' => $mode,
                    'custom_percent' => null,
                ];
            }
        }

        $record->syncPromoServices($rows);
    }
}
