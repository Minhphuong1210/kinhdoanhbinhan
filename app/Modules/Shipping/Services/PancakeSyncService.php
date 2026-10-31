<?php

namespace App\Modules\Shipping\Services;

use App\Models\PancakeCommunes;
use App\Models\PancakeDistricts;
use App\Models\PancakeMapProvince;
use App\Models\PancakeProvinces;
use App\Models\Product;
use App\Models\Provinces;
use App\Modules\Shipping\Models\ShippingCarrier;
use App\Modules\Shipping\Repositories\ShippingCarrierRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

/**
 * Đồng bộ dữ liệu từ Pancake POS: shop, kho, sản phẩm, địa giới hành chính.
 */
class PancakeSyncService
{
    private const API_URL = 'https://pos.pages.fm/api/v1';

    public function __construct(private readonly ShippingCarrierRepository $carriers)
    {
    }

    public function carrier(): ?ShippingCarrier
    {
        return $this->carriers->activePancake();
    }

    public function shops(ShippingCarrier $carrier): ?array
    {
        return $this->get($carrier, '/shops');
    }

    public function warehouses(ShippingCarrier $carrier): ?array
    {
        return $this->get($carrier, '/shops/' . $this->shopId($carrier) . '/warehouses');
    }

    public function syncProducts(ShippingCarrier $carrier): array
    {
        $data = $this->get($carrier, '/shops/' . $this->shopId($carrier) . '/products');
        $items = $data['data'] ?? [];
        $updated = 0;
        $notFound = 0;

        foreach ($items as $item) {
            $product = !empty($item['custom_id'])
                ? Product::where('slug', $item['custom_id'])->first()
                : null;

            if (!$product) {
                $notFound++;
                continue;
            }

            $product->update([
                'product_pancake_id'   => $item['id'] ?? null,
                'variation_pancake_id' => data_get($item, 'variations.0.id'),
            ]);

            $updated++;
        }

        return [
            'success'   => true,
            'total_api' => count($items),
            'updated'   => $updated,
            'not_found' => $notFound,
            'data'      => $data,
        ];
    }

    public function syncProvinces(ShippingCarrier $carrier): int
    {
        $data = $this->get($carrier, '/geo/provinces', [
            'country_code' => '84',
            'is_new'       => 'false',
            'all'          => 'true',
        ]);

        foreach (($data['data'] ?? []) as $item) {
            PancakeProvinces::create([
                'country_code' => $item['country_code'] ?? null,
                'id_pancake'   => $item['id'] ?? null,
                'name'         => $item['name'] ?? '',
                'name_en'      => $item['name_en'] ?? null,
                'new_id'       => $item['new_id'] ?? null,
                'region_type'  => $item['region_type'] ?? null,
            ]);
        }

        return count($data['data'] ?? []);
    }

    public function syncDistricts(ShippingCarrier $carrier): void
    {
        foreach (PancakeProvinces::query()->whereNotNull('new_id')->get() as $province) {
            $data = $this->get($carrier, '/geo/districts', ['province_id' => $province->id_pancake]);

            foreach (($data['data'] ?? []) as $item) {
                PancakeDistricts::create([
                    'id_pancake'  => $item['id'] ?? null,
                    'province_id' => $item['province_id'] ?? null,
                    'name'        => $item['name'] ?? '',
                    'name_en'     => $item['name_en'] ?? null,
                ]);
            }
        }
    }

    /** @return array{district_processed: int, communes_inserted: int} */
    public function syncCommunes(ShippingCarrier $carrier): array
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $processed = 0;
        $inserted = 0;

        PancakeDistricts::query()->orderBy('id')->chunkById(50, function ($districts) use ($carrier, &$processed, &$inserted) {
            foreach ($districts as $district) {
                try {
                    $data = $this->get($carrier, '/geo/communes', [
                        'district_id' => $district->id_pancake,
                        'province_id' => $district->province_id,
                    ]);

                    foreach (($data['data'] ?? []) as $item) {
                        $row = PancakeCommunes::firstOrCreate(
                            ['id_pancake' => $item['id']],
                            [
                                'province_id' => $item['province_id'] ?? null,
                                'district_id' => $item['district_id'] ?? null,
                                'name'        => $item['name'] ?? '',
                                'name_en'     => $item['name_en'] ?? null,
                                'new_id'      => $item['new_id'] ?? null,
                                'postcode'    => $item['postcode'] ?? null,
                            ]
                        );

                        if ($row->wasRecentlyCreated) {
                            $inserted++;
                        }
                    }

                    $processed++;

                    // nghỉ nhẹ để tránh bị Pancake chặn
                    usleep(150000);
                } catch (\Throwable $e) {
                    logger()->error('Pancake commune sync', [
                        'district_id' => $district->id_pancake,
                        'message'     => $e->getMessage(),
                    ]);
                }
            }
        });

        return ['district_processed' => $processed, 'communes_inserted' => $inserted];
    }

    /** Ghép tỉnh của mình với tỉnh Pancake theo tên đã chuẩn hóa */
    public function mapProvinces(): array
    {
        $pancakes = PancakeProvinces::query()
            ->whereNotNull('new_id')
            ->get()
            ->keyBy(fn ($item) => $this->normalizeProvinceName($item->name));

        $matched = 0;
        $notMatched = [];

        foreach (Provinces::all() as $province) {
            $pancake = $pancakes->get($this->normalizeProvinceName($province->name));

            if (!$pancake) {
                $notMatched[] = ['province_id' => $province->id, 'name' => $province->name];
                continue;
            }

            PancakeMapProvince::updateOrCreate(
                ['province_id' => $province->id],
                ['pancake_id' => $pancake->id_pancake, 'pancake_new_id' => $pancake->new_id]
            );

            $matched++;
        }

        return ['success' => true, 'matched' => $matched, 'not_matched' => $notMatched];
    }

    private function normalizeProvinceName(string $name): string
    {
        $name = str_replace(['Tỉnh ', 'Thành phố ', 'TP ', 'TP. '], '', trim($name));

        return preg_replace('/\s+/', '', Str::ascii(mb_strtolower($name)));
    }

    private function shopId(ShippingCarrier $carrier): int|string
    {
        return $carrier->config['shop_id'] ?? '';
    }

    private function get(ShippingCarrier $carrier, string $path, array $query = []): ?array
    {
        $response = (new Client(['timeout' => 30]))->request('GET', self::API_URL . $path, [
            'query' => $query + ['api_key' => $carrier->api_token],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
