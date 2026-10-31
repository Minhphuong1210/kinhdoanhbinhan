<?php

namespace App\Modules\Core\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryPost;
use App\Models\GioiThieu;
use App\Models\Leader;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Widget;

class HomeController extends Controller
{
    public function index()
    {
        // Thông tin doanh nghiệp lấy qua FrontendLayoutComposer (chỉ các key công khai)
        $data = cache()->remember('homepage_data', 3600, function () {

            $products = Product::where('hien_trang_chu', 1)
                ->where('active', 1)
                ->availableForPurchase()
                ->latest()
                ->take(16)
                ->get();

            if ($products->isEmpty()) {
                $products = Product::where('active', 1)
                    ->availableForPurchase()
                    ->latest()
                    ->take(16)
                    ->get();
            }

            $topProducts = Product::where('active', 1)
                ->availableForPurchase()
                ->where('san_pham_noi_bat', 1)
                ->orderByDesc('view')
                ->latest()
                ->take(8)
                ->get();

            if ($topProducts->isEmpty()) {
                $topProducts = Product::where('active', 1)
                    ->availableForPurchase()
                    ->orderByDesc('view')
                    ->latest()
                    ->take(8)
                    ->get();
            }

            $comboProducts = Product::where('active', 1)
                ->availableForPurchase()
                ->where(function ($query) {
                    $query->where('name', 'like', '%combo%')
                        ->orWhere('slug', 'like', '%combo%');
                })
                ->latest()
                ->take(8)
                ->get();

            if ($comboProducts->isEmpty()) {
                $comboProducts = Product::where('active', 1)
                    ->availableForPurchase()
                    ->whereNotNull('old_price')
                    ->whereColumn('old_price', '>', 'price')
                    ->latest()
                    ->take(8)
                    ->get();
            }

            $about_us = GioiThieu::forWeb()
                ->where('vi_tri', 've_chung_toi')
                ->first();

            if (!$about_us) {
                $about_us = GioiThieu::forWeb()->first();
            }

            $cauChuyen = GioiThieu::forWeb()
                ->where('vi_tri', '!=', 'gioi_thieu_5')
                ->orderBy('id')
                ->get();

            $posts = Post::forWeb()
                ->where(function ($query) {
                    $query->where('status', 1)
                        ->orWhere('status', 'published');
                })
                ->latest()
                ->take(10)
                ->get();

            $categories = CategoryPost::where('active', 1)->get();

            $latestOrders = Order::with('user')
                ->where('status', 'completed')
                ->latest()
                ->take(20)
                ->get();

            $leaders = Leader::where('status', 1)
                ->orderBy('order_no', 'desc')
                ->limit(8)
                ->get();

            $productCategories = Category::where('active', 1)
                ->whereNull('parent_id')
                ->with(['children' => function ($q) {
                    $q->where('active', 1)->orderBy('name');
                }])
                ->withCount(['products' => function ($q) {
                    $q->where('active', 1)->availableForPurchase();
                }])
                ->orderBy('name')
                ->get();

            $categorySections = $productCategories
                ->take(4)
                ->map(function (Category $category) {
                    $childIds = Category::where('parent_id', $category->id)->pluck('id')->all();

                    $homeProducts = Product::where('active', 1)
                        ->availableForPurchase()
                        ->where(function ($query) use ($category, $childIds) {
                            $query->where('category_id', $category->id);

                            if (!empty($childIds)) {
                                $query->orWhereIn('category_id', $childIds);
                            }
                        })
                        ->latest()
                        ->take(6)
                        ->get();

                    $category->setRelation('homeProducts', $homeProducts);

                    return $category;
                })
                ->filter(fn (Category $category) => $category->homeProducts->isNotEmpty())
                ->values();

            $heroBanners = Widget::forWeb()
                ->where('status', 1)
                ->whereIn('location', [
                    'homepage-banner',
                    'home-banner',
                    'banner-home',
                    'slider-home',
                    'slide-home',
                    'hero-home',
                ])
                ->orderBy('order_no')
                ->take(6)
                ->get();

            $homeBenefits = Widget::forWeb()
                ->where('status', 1)
                ->whereIn('location', [
                    'home-benefit',
                    'home-benefits',
                    'loi_ich_home',
                    'vi_sao_chon_chung_toi',
                ])
                ->orderBy('order_no')
                ->take(4)
                ->get();

            $homeStats = [
                'product_count' => Product::where('active', 1)->availableForPurchase()->count(),
                'category_count' => Category::where('active', 1)->count(),
                'completed_order_count' => Order::where('status', 'completed')->count(),
            ];

            $kinh_doanh = Widget::where('location', 'mo_hinh_kinh_doanh_home')
                ->where('status', 1)
                ->first();

            $cam_nhan = Widget::where(function ($query) {
                    $query->where('location', 'Cam nhan khach hang')
                        ->orWhere('location', 'Cảm nhận khách hàng');
                })
                ->where('status', 1)
                ->orderBy('order_no', 'desc')
                ->limit(6)
                ->get();

            return compact(
                'products',
                'topProducts',
                'comboProducts',
                'about_us',
                'cauChuyen',
                'posts',
                'categories',
                'latestOrders',
                'leaders',
                'kinh_doanh',
                'cam_nhan',
                'productCategories',
                'categorySections',
                'heroBanners',
                'homeBenefits',
                'homeStats'
            );
        });

        return view('Core::Frontend.home.index', $data);
    }
}
