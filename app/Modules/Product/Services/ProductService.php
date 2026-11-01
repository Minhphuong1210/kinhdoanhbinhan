<?php

namespace App\Modules\Product\Services;

use App\Models\Product;
use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Admin quản lý sản phẩm: thông tin, ảnh, giấy phép, tag.
 */
class ProductService extends BaseService
{
    private const FLAGS = ['active', 'hien_trang_chu', 'san_pham_noi_bat', 'san_pham_hien_nhu_baner', 'san_pham_hoan'];

    protected ?string $uploadFolder = 'products';

    public function __construct(ProductRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(?string $search, mixed $categoryId): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($search, $categoryId);
    }

    public function options(?int $exceptId = null)
    {
        return $this->repository->options($exceptId);
    }

    public function create(array $data): Model
    {
        $tags = Arr::pull($data, 'tags');
        $extra = Arr::pull($data, 'image_extra', []);
        $licenses = Arr::pull($data, 'licenses', []);

        $slug = Str::slug($data['name']);
        $data['slug'] = $this->repository->slugExists($slug) ? $slug . '-' . random_int(1000, 9999) : $slug;

        if (!empty($extra)) {
            $data['image_extra'] = $this->uploadAll($extra, 'products/extra');
        }

        if (!empty($licenses)) {
            $data['licenses'] = $this->uploadAll($licenses, 'products/licenses');
        }

        $product = parent::create($data);

        if (!empty($tags)) {
            $product->tags()->sync($tags);
        }

        Cache::forget('homepage_data');

        return $product;
    }

    public function update(Model $model, array $data): Model
    {
        $tags = Arr::pull($data, 'tags', []);
        $newExtra = Arr::pull($data, 'image_extra', []);
        $newLicenses = Arr::pull($data, 'licenses', []);
        $keepExtra = Arr::pull($data, 'keep_extra', []);
        $keepLicenses = Arr::pull($data, 'keep_licenses', []);

        $data['slug'] = Str::slug($data['name']);

        if (($data['image'] ?? null) instanceof UploadedFile && $model->image) {
            cdn_delete($model->image);
        }

        $data['image_extra'] = $this->mergeGallery($model->image_extra ?? [], (array) $keepExtra, $newExtra, 'products/extra');
        $data['licenses'] = $this->mergeGallery($model->licenses ?? [], (array) $keepLicenses, $newLicenses, 'products/licenses');

        parent::update($model, $data);
        $model->tags()->sync($tags ?? []);
        Cache::forget('homepage_data');

        return $model;
    }

    /** Xoá sản phẩm cùng toàn bộ hình ảnh */
    public function delete(Model $model): void
    {
        $paths = array_merge([$model->image], $model->image_extra ?? [], $model->licenses ?? []);

        foreach (array_filter($paths) as $path) {
            cdn_delete($path);
        }

        $this->deleteEditorImages($model->content);
        $this->deleteEditorImages($model->instructions);

        $model->tags()->detach();
        parent::delete($model);

        Cache::forget('homepage_data');
    }

    public function uploadContentImage(UploadedFile $file): string
    {
        return cdn_upload($file, 'products/content');
    }

    protected function prepare(array $data): array
    {
        foreach (self::FLAGS as $flag) {
            $data[$flag] = $this->flag($data, $flag);
        }

        // Chỉ admin được sửa giá vốn và cơ chế hoàn phí
        if (!auth()->user()?->is_admin) {
            unset($data['purchase_price'], $data['co_che_hoan_phi_id']);
        }

        $data['video'] = ($data['video'] ?? null) ?: null;

        return $data;
    }

    /** Giữ ảnh được chọn, xoá ảnh bị bỏ, thêm ảnh mới */
    private function mergeGallery(array $existing, array $keep, array $newFiles, string $folder): ?array
    {
        $kept = array_values(array_filter($existing, fn ($path) => in_array($path, $keep)));

        foreach (array_diff($existing, $kept) as $path) {
            cdn_delete($path);
        }

        $gallery = array_merge($kept, $this->uploadAll($newFiles, $folder));

        return $gallery ?: null;
    }

    private function uploadAll(array $files, string $folder): array
    {
        return array_values(array_map(fn (UploadedFile $file) => cdn_upload($file, $folder), array_filter($files)));
    }
}
