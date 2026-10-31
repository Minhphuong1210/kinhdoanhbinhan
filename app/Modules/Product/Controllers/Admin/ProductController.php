<?php

namespace App\Modules\Product\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CoCheHoanPhi;
use App\Models\Product;
use App\Modules\Tag\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:product.view')->only(['index']);
        $this->middleware('permission:product.create')->only(['create', 'store']);
        $this->middleware('permission:product.edit')->only(['edit', 'update', 'image']);
        $this->middleware('permission:product.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Product::with('category', 'coCheHoanPhi');
        if ($search = $request->search) {
            $query->where('name', 'like', "%$search%");
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $products = $query->orderBy('stt', 'asc')->latest()->paginate(20)->withQueryString();
        $categories = Category::whereNull('parent_id')->get();
        return view('Product::Admin.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        $products = Product::orderBy('name')->get(['id', 'name']);
        $coCheHoanPhis = CoCheHoanPhi::orderBy('name')->get();
        return view('Product::Admin.create', compact('categories', 'tags', 'products', 'coCheHoanPhis'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'price_hoan_tien' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'parent_id' => 'nullable|exists:products,id',
            'co_che_hoan_phi_id' => 'nullable|exists:co_che_hoan_phi,id',
            'image' => 'nullable|image|max:3072',
            'image_extra.*' => 'nullable|image|max:3072',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'instructions' => 'nullable|string',
            'origin' => 'nullable|string',
'stt'=>'nullable|int',
            'video' => 'nullable|string|max:500',
        ]);
        if (!auth()->user()->is_admin) {
            unset($data['purchase_price']);
            unset($data['co_che_hoan_phi_id']);
        }
        $data['video'] = $request->input('video') ?: null;
//        $data['slug'] = Str::slug($data['name']);

        $slug = Str::slug($data['name']);

        if (\App\Models\Product::where('slug', $slug)->exists()) {
            $slug .= '-' . rand(1000, 9999);
        }

        $data['slug'] = $slug;

        $data['active'] = $request->boolean('active');
        $data['hien_trang_chu'] = $request->boolean('hien_trang_chu');
        $data['san_pham_noi_bat'] = $request->boolean('san_pham_noi_bat');
        $data['san_pham_hien_nhu_baner'] = $request->boolean('san_pham_hien_nhu_baner');
        $data['san_pham_hoan'] = $request->boolean('san_pham_hoan');


        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'products');
        }
        // Ảnh phụ — truyền array thẳng, model cast tự encode JSON
        if ($request->hasFile('image_extra')) {
            $extraPaths = [];
            foreach ($request->file('image_extra') as $file) {
                $extraPaths[] = cdn_upload($file, 'products/extra');
            }
            $data['image_extra'] = $extraPaths;
        }

        // Giấy phép (multiple images)
        if ($request->hasFile('licenses')) {
            $licensePaths = [];
            foreach ($request->file('licenses') as $file) {
                $licensePaths[] = cdn_upload($file, 'products/licenses');
            }
            $data['licenses'] = $licensePaths;
        }

        $product = Product::create($data);
        if ($request->filled('tags')) {
            $product->tags()->sync($request->tags);
        }


        Cache::forget('homepage_data');


        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $tags = Tag::all();
        $products = Product::where('id', '!=', $product->id)->orderBy('name')->get(['id', 'name']);
        $coCheHoanPhis = CoCheHoanPhi::orderBy('name')->get();
        $extraImages = $product->image_extra ?? []; // already decoded via cast
        $product->load('tags');
        return view('Product::Admin.edit', compact('product', 'categories', 'tags', 'extraImages', 'products', 'coCheHoanPhis'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'price' => 'nullable|numeric|min:0',
            'quantity' => 'nullable|integer|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'price_hoan_tien' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'parent_id' => 'nullable|exists:products,id',
            'co_che_hoan_phi_id' => 'nullable|exists:co_che_hoan_phi,id',
            'image' => 'nullable|image|max:3072',
            'image_extra.*' => 'nullable|image|max:3072',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'instructions' => 'nullable|string',
            'origin' => 'nullable|string',
            'stt'=>'nullable|int',
            'video' => 'nullable|string|max:500',
        ]);
        if (!auth()->user()->is_admin) {
            unset($data['purchase_price']);
            unset($data['co_che_hoan_phi_id']);
        }

        $data['video'] = $request->input('video') ?: null;
        $data['slug'] = Str::slug($data['name']);

        $data['active'] = $request->boolean('active');
        $data['hien_trang_chu'] = $request->boolean('hien_trang_chu');
        $data['san_pham_noi_bat'] = $request->boolean('san_pham_noi_bat');
        $data['san_pham_hien_nhu_baner'] = $request->boolean('san_pham_hien_nhu_baner');
        $data['san_pham_hoan'] = $request->boolean('san_pham_hoan');

        /*
         |--------------------------------------------------------------------------
         | Ảnh chính
         |--------------------------------------------------------------------------
         */
        if ($request->hasFile('image')) {
            // xoá ảnh cũ
            if ($product->image) {
                cdn_delete($product->image);
            }

            $data['image'] = cdn_upload($request->file('image'), 'products');
        }

        /*
         |--------------------------------------------------------------------------
         | Ảnh phụ
         |--------------------------------------------------------------------------
         */

        $existingExtra = $product->image_extra ?? [];

        // ảnh giữ lại từ form
        $keepImages = $request->input('keep_extra', []);

        // lọc ảnh cũ cần giữ
        $keptOld = array_values(array_filter(
            $existingExtra,
            fn($p) => in_array($p, $keepImages)
        ));

        // xoá ảnh bị bỏ
        $deletedImages = array_diff($existingExtra, $keptOld);
        foreach ($deletedImages as $img) {
            cdn_delete($img);
        }

        // thêm ảnh mới
        if ($request->hasFile('image_extra')) {
            foreach ($request->file('image_extra') as $file) {
                $keptOld[] = cdn_upload($file, 'products/extra');
            }
        }

        $data['image_extra'] = $keptOld ?: null;

        /*
         |--------------------------------------------------------------------------
         | Giấy phép (multiple images)
         |--------------------------------------------------------------------------
         */
        $existingLicenses = $product->licenses ?? [];
        $keepLicenses = $request->input('keep_licenses', []);
        $keptOldLicenses = array_values(array_filter(
            $existingLicenses,
            fn($p) => in_array($p, $keepLicenses)
        ));

        // xoá ảnh bị bỏ
        $deletedLicenses = array_diff($existingLicenses, $keptOldLicenses);
        foreach ($deletedLicenses as $img) {
            cdn_delete($img);
        }

        // thêm ảnh mới
        if ($request->hasFile('licenses')) {
            foreach ($request->file('licenses') as $file) {
                $keptOldLicenses[] = cdn_upload($file, 'products/licenses');
            }
        }
        $data['licenses'] = $keptOldLicenses ?: null;

        /*
         |--------------------------------------------------------------------------
         | Update
         |--------------------------------------------------------------------------
         */
        $product->update($data);
        $product->tags()->sync($request->tags ?? []);
        Cache::forget('homepage_data');
        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {

        if ($product->image) {
            cdn_delete($product->image);
        }

        foreach ($product->image_extra ?? [] as $image) {
            cdn_delete($image);
        }

        foreach ($product->licenses ?? [] as $image) {
            cdn_delete($image);
        }

        $this->deleteEditorImages($product->content);

        $this->deleteEditorImages($product->instructions);

        $product->tags()->detach();

        $product->delete();

        Cache::forget('homepage_data');

        return back()->with('success', 'Xóa sản phẩm và toàn bộ hình ảnh thành công!');
    }

    public function image(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:5120',
        ]);

        $file = $request->file('upload');

        $path = cdn_upload($file, 'products/content');

        return response()->json([
            'uploaded' => 1,
            'fileName' => basename($path),
            'url'      => cdn($path),
        ]);
    }

    private function deleteEditorImages(?string $html): void
    {
        if (empty($html)) {
            return;
        }

        preg_match_all(
            '/<img[^>]+src=["\']([^"\']+)["\']/i',
            $html,
            $matches
        );

        if (empty($matches[1])) {
            return;
        }

        foreach ($matches[1] as $url) {
            cdn_delete($url);
        }
    }
}
