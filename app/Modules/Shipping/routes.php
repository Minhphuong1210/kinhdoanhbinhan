<?php

use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
use App\Models\ShippingCarrier;
use App\Models\PancakeProvinces;
use App\Models\PancakeDistricts;
use App\Models\PancakeCommunes;
use App\Models\Provinces;
use Illuminate\Support\Str;
use App\Models\PancakeMapProvince;
use App\Models\Product;
Route::group([
    'prefix' => 'admin/shipping',
    'as' => 'admin.shipping.',
    'namespace' => 'App\Modules\Shipping\Controllers\Admin',
    'middleware' => ['web', 'auth', 'admin']
], function () {
    Route::get('settings', 'ShippingController@settings')->name('settings');
    Route::post('settings', 'ShippingController@saveSettings')->name('settings.save');
});

// lấy shop ra

Route::get('/test-pancake-shops', function () {

    try {

        $carrier = ShippingCarrier::where('alias', 'pancake')
            ->where('is_active', 1)
            ->first();

        if (!$carrier) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy cấu hình Pancake'
            ]);
        }

        $client = new Client();

        $response = $client->request('GET', 'https://pos.pages.fm/api/v1/shops', [
            'query' => [
                'api_key' => $carrier->api_token
            ]
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        return response()->json($result);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
});

// lấy kho ra

Route::get('/test-pancake-warehouses', function () {

    $carrier = ShippingCarrier::where('alias', 'pancake')
        ->where('is_active', 1)
        ->first();

    if (!$carrier) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy cấu hình Pancake'
        ]);
    }


    $client = new \GuzzleHttp\Client();

    $response = $client->request('GET',
        'https://pos.pages.fm/api/v1/shops/1022017886/warehouses',
        [
            'query' => [
                'api_key' => $carrier->api_token
            ]
        ]
    );

    return json_decode($response->getBody(), true);
});

// lấy thông tin sản phẩm
Route::get('/test-pancake-products', function () {

    $carrier = \App\Models\ShippingCarrier::where('alias', 'pancake')
        ->where('is_active', 1)
        ->first();

    if (!$carrier) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy cấu hình Pancake'
        ]);
    }

    $client = new \GuzzleHttp\Client();

    $response = $client->request(
        'GET',
        'https://pos.pages.fm/api/v1/shops/1022017886/products',
        [
            'query' => [
                'api_key' => $carrier->api_token
            ]
        ]
    );

    $data = json_decode($response->getBody()->getContents(), true);

    $products = $data['data'] ?? [];

    $updated = 0;
    $notFound = 0;

    foreach ($products as $item) {

        $slug = $item['custom_id'] ?? null;

        if (!$slug) {
            $notFound++;
            continue;
        }

        $product = Product::where('slug', $slug)->first();

        if (!$product) {
            $notFound++;
            continue;
        }

        $product->update([
            'product_pancake_id' => $item['id'] ?? null,
            'variation_pancake_id' => data_get($item, 'variations.0.id'),
        ]);

        $updated++;
    }

    return response()->json([
        'success' => true,
        'total_api' => count($products),
        'updated' => $updated,
        'not_found' => $notFound,
        'data' => $data,
    ]);
});

// lấy thành phố

Route::get('/test-pancake-provinces', function () {

    $carrier = ShippingCarrier::where('alias', 'pancake')
        ->where('is_active', 1)
        ->first();

    if (!$carrier) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy cấu hình Pancake'
        ]);
    }

    $client = new Client();

    $response = $client->request(
        'GET',
        'https://pos.pages.fm/api/v1/geo/provinces',
        [
            'query' => [
                'country_code' => '84',
                'is_new' => 'false',
                'all' => 'true',
                'api_key' => $carrier->api_token,
            ]
        ]
    );

    $data = json_decode($response->getBody(), true);

    foreach (($data['data'] ?? []) as $item) {

        PancakeProvinces::create([
            'country_code' => $item['country_code'] ?? null,
            'id_pancake' => $item['id'] ?? null,
            'name' => $item['name'] ?? '',
            'name_en' => $item['name_en'] ?? null,
            'new_id' => $item['new_id'] ?? null,
            'region_type' => $item['region_type'] ?? null,
        ]);
    }

    return response()->json([
        'success' => true,
        'total' => count($data['data'] ?? []),
    ]);
});

//lấy tính thành

Route::get('/test-pancake-districts', function () {

    $carrier = ShippingCarrier::where('alias', 'pancake')
        ->where('is_active', 1)
        ->first();

    if (!$carrier) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy cấu hình Pancake'
        ]);
    }

    $client = new Client();

    $provinces = PancakeProvinces::query()
        ->whereNotNull('new_id')
        ->get();

    foreach ($provinces as $province) {

        $response = $client->request(
            'GET',
            'https://pos.pages.fm/api/v1/geo/districts',
            [
                'query' => [

                    'province_id' => $province->id_pancake,
                    'api_key' => $carrier->api_token,
                ]
            ]
        );

        $data = json_decode(
            $response->getBody()->getContents(),
            true
        );
//dd($data);
        foreach (($data['data'] ?? []) as $item) {



            PancakeDistricts::create([
                'id_pancake' => $item['id'] ?? null,
                'province_id' => $item['province_id'] ?? null,
                'name' => $item['name'] ?? '',
                'name_en' => $item['name_en'] ?? null,
            ]);
        }
    }

    return response()->json([
        'success' => true,

    ]);
});

// lấy quận huyện

Route::get('/test-pancake-communes', function () {

    $carrier = ShippingCarrier::where('alias', 'pancake')
        ->where('is_active', 1)
        ->first();

    if (!$carrier) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy cấu hình Pancake',
        ]);
    }

    set_time_limit(0);
    ini_set('memory_limit', '1024M');

    $client = new Client([
        'timeout' => 30,
    ]);

    $processed = 0;
    $inserted = 0;

    PancakeDistricts::query()
        ->orderBy('id')
        ->chunkById(50, function ($districts)
        use (
            $client,
            $carrier,
            &$processed,
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

                        $row = PancakeCommunes::firstOrCreate(
                            [
                                'id_pancake' => $item['id'],
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
                        'message' => $e->getMessage(),
                    ]);
                }
            }
        });

    return response()->json([
        'success' => true,
        'district_processed' => $processed,
        'communes_inserted' => $inserted,
    ]);
});

// map 2 bảng vào với nhau


Route::get('/map-pancake-provinces', function () {

    // lấy pancake có new_id thôi
    $pancakes = PancakeProvinces::query()
        ->whereNotNull('new_id')
        ->get();

    $matched = 0;
    $notMatched = [];

    foreach (Provinces::all() as $province) {

        // chuẩn hóa tên bên mình
        $localName = trim($province->name);

        $localName = str_replace([
            'Tỉnh ',
            'Thành phố ',
            'TP ',
            'TP. ',
        ], '', $localName);

        $localName = mb_strtolower($localName);
        $localName = Str::ascii($localName);
        $localName = preg_replace('/\s+/', '', $localName);

        // tìm bên Pancake
        $pancake = $pancakes->first(function ($item) use ($localName) {

            $pancakeName = trim($item->name);

            $pancakeName = str_replace([
                'Tỉnh ',
                'Thành phố ',
                'TP ',
                'TP. ',
            ], '', $pancakeName);

            $pancakeName = mb_strtolower($pancakeName);
            $pancakeName = Str::ascii($pancakeName);
            $pancakeName = preg_replace('/\s+/', '', $pancakeName);

            return $pancakeName === $localName;
        });

        // không thấy thì bỏ qua
        if (! $pancake) {

            $notMatched[] = [
                'province_id' => $province->id,
                'name' => $province->name,
            ];

            continue;
        }

        // lưu map
        PancakeMapProvince::updateOrCreate(
            [
                'province_id' => $province->id,
            ],
            [
                'pancake_id' => $pancake->id_pancake,
                'pancake_new_id' => $pancake->new_id,
            ]
        );

        $matched++;
    }

    return response()->json([
        'success' => true,
        'matched' => $matched,
        'not_matched' => $notMatched,
    ]);
});



