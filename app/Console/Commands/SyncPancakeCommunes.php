<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Models\ShippingCarrier;
use App\Models\PancakeDistricts;
use App\Models\PancakeCommunes;

class SyncPancakeCommunes extends Command
{
    protected $signature = 'pancake:sync-communes';

    protected $description = 'Sync Pancake communes';

    public function handle()
    {
        $carrier = ShippingCarrier::where('alias', 'pancake')
            ->where('is_active', 1)
            ->first();

        if (!$carrier) {
            $this->error('Không tìm thấy Pancake');
            return 1;
        }

        $client = new Client([
            'timeout' => 30,
        ]);

        $inserted = 0;

        PancakeDistricts::query()
            ->orderBy('id')
            ->chunkById(50, function ($districts)
            use (
                $client,
                $carrier,
                &$inserted
            ) {

                foreach ($districts as $district) {

                    try {

                        $response = $client->request(
                            'GET',
                            'https://pos.pages.fm/api/v1/geo/communes',
                            [
                                'query' => [
                                    'district_id' => $district->id_pancake,
                                    'province_id' => $district->province_id,
                                    'api_key' => $carrier->api_token,
                                ]
                            ]
                        );

                        $data = json_decode(
                            $response->getBody()->getContents(),
                            true
                        );

                        foreach (($data['data'] ?? []) as $item) {

                            PancakeCommunes::firstOrCreate(
                                [
                                    'id_pancake' => $item['id']
                                ],
                                [
                                    'province_id' => $item['province_id'] ?? null,
                                    'district_id' => $item['district_id'] ?? null,
                                    'name' => $item['name'] ?? '',
                                    'name_en' => $item['name_en'] ?? null,
                                    'new_id' => $item['new_id'] ?? null,
                                    'postcode' => $item['postcode'] ?? null,
                                ]
                            );

                            $inserted++;
                        }

                        $this->info(
                            'Done district: '
                            . $district->name
                        );

                        usleep(150000);

                    } catch (\Throwable $e) {

                        $this->error(
                            $district->name
                            .' => '
                            .$e->getMessage()
                        );
                    }
                }
            });

        $this->info(
            'Inserted: ' . $inserted
        );

        return 0;
    }
}