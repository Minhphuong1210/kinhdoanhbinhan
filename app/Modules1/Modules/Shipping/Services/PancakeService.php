<?php
namespace App\Modules\Shipping\Services;

use App\Models\Order;
use App\Models\ShippingCarrier;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class PancakeService
{
    protected $apiUrl = 'https://pos.pages.fm/api/v1';
    protected $carrier;

    public function __construct()
    {
        $this->carrier = ShippingCarrier::where('alias', 'pancake')->where('is_active', 1)->first();
    }

    public function pushOrder(Order $order)
    {
        if (! $this->carrier) {
            Log::warning("Pancake carrier not configured.");
            return false;
        }

        $shopId = $this->carrier->config['shop_id'] ?? null;
        $token  = $this->carrier->api_token;

        if (! $shopId || ! $token) {
            Log::error("Pancake Shop ID or API Token missing.");
            return false;
        }

        $client = new Client();

        $endpoint = "{$this->apiUrl}/shops/{$shopId}/orders";

        // load product luôn
        $order->load([
            'details.product',
            'pancakeProvince',
            'pancakeDistrict',
            'pancakeWard',
        ]);

        $items = [];

        foreach ($order->details as $detail) {

            $product = $detail->product;


            Log::info(" product", [
                'product' => $product,
            ]);

            if (! $product) {
                Log::warning("Không tìm thấy product", [
                    'order_detail_id' => $detail->id,
                ]);
                continue;
            }

            if (
                empty($product->product_pancake_id) ||
                empty($product->variation_pancake_id)
            ) {
                Log::warning("Thiếu pancake id", [
                    'product_id' => $product->id,
                ]);
                continue;
            }

            $items[] = [
                'discount_each_product' => 0,
                'is_bonus_product'      => false,
                'is_discount_percent'   => false,
                'is_wholesale'          => false,
                'one_time_product'      => false,

                'quantity'              => (int) $detail->quantity,

                'product_id'            => $product->product_pancake_id,
                'variation_id'          => $product->variation_pancake_id,

                'variation_info'        => [
                    'detail'             => null,
                    'fields'             => [],
                    'display_id'         => (string) $product->id,
                    'name'               => $product->name,
                    'product_display_id' => (string) $product->id,
                    'retail_price'       => (int) 0,
                    'weight'             => $product->khoi_luong ?? 300,
                ],
            ];
        }

        if (empty($items)) {
            Log::error("Không có item nào để push Pancake");
            return false;
        }

        $payload = [

            'bill_full_name'    => $order->receive_user,
            'bill_phone_number' => $order->receive_phone,

            'is_free_shipping'  => false,
            'received_at_shop'  => false,

            'shipping_fee'      => 0,
            'cash'              => 0,
            'status'            => 0,
            'merge_order'       => false,
            'total_discount'    => 0,

            'warehouse_id'      => '9342050d-25d3-4406-a5d7-c5727f079d5a',

            'shipping_address'  => [
                'address'      => $order->receive_address,
                'full_address' => $order->receive_address,
                'full_name'    => $order->receive_user,
                'phone_number' => $order->receive_phone,
                'province_id'  => $order->province_id,
                'district_id'  => $order->district_id,
                'commune_id'   => $order->ward_id,
            ],

            'warehouse_info'    => [
                'district_id'  => $order->district_id,
                'full_address' => $order->receive_address,
                'name'         => 'best express',
                'phone_number' => $order->receive_phone,
                'province_id'  => $order->province_id,
            ],

            'items'             => $items,

            'note'              =>  'Gọi trước khi giao hàng, không gọi được khách liên hệ lại shop. Nếu có gửi hàng thì báo lại cho khách . Xin cảm ơn',

            'shop_id'           => (int) $shopId,
        ];

        Log::info("Pancake carrier push order", $payload);

        try {

            $response = $client->request('POST', $endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'query'   => [
                    'api_key' => $token,
                ],
                'json'    => $payload,
            ]);

            $result = json_decode($response->getBody(), true);

            Log::info("Pancake response", $result);

            return $result;

        } catch (\Throwable $e) {

            Log::error("Pancake Exception", [
                'message' => $e->getMessage(),
                'payload' => $payload,
            ]);

            return false;
        }
    }
}
