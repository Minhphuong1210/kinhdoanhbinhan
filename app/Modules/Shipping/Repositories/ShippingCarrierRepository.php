<?php

namespace App\Modules\Shipping\Repositories;

use App\Modules\Shared\Repositories\BaseRepository;
use App\Modules\Shipping\Models\ShippingCarrier;

class ShippingCarrierRepository extends BaseRepository
{
    private const PANCAKE = 'pancake';

    protected string $model = ShippingCarrier::class;

    public function pancake(): ?ShippingCarrier
    {
        return $this->query()->where('alias', self::PANCAKE)->first();
    }

    public function activePancake(): ?ShippingCarrier
    {
        return $this->query()->where('alias', self::PANCAKE)->where('is_active', 1)->first();
    }

    public function savePancake(string $apiToken, string $shopId, bool $isActive): ShippingCarrier
    {
        return $this->query()->updateOrCreate(
            ['alias' => self::PANCAKE],
            [
                'name'      => 'Pancake POS',
                'api_token' => $apiToken,
                'config'    => ['shop_id' => $shopId],
                'is_active' => $isActive,
            ]
        );
    }
}
