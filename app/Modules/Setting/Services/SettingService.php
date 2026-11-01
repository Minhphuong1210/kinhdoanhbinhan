<?php

namespace App\Modules\Setting\Services;

use App\Models\Setting;
use App\Modules\Home\ViewComposers\FrontendLayoutComposer;
use App\Modules\Setting\Repositories\SettingRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SettingService extends BaseService
{
    private const MAX_FILE_SIZE = 2 * 1024 * 1024;
    private const VIDEO_EXTENSIONS = ['mp4', 'webm', 'ogg'];
    // Không nhận svg: file svg có thể chứa script
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    protected ?string $uploadFolder = 'uploads/settings';

    public function __construct(SettingRepository $repository)
    {
        parent::__construct($repository);
    }

    /** Nhóm setting theo type: tab general lên đầu, còn lại theo alphabet */
    public function groupedForAdmin(): Collection
    {
        $all = $this->repository->groupedForWeb();
        $settings = collect();

        if ($all->has('general')) {
            $settings->put('general', $all->get('general'));
        }

        foreach ($all->sortKeys() as $key => $group) {
            if ($key !== 'general') {
                $settings->put($key, $group);
            }
        }

        return $settings;
    }

    /**
     * @param array $values [id => giá trị] của setting dạng text
     * @param array $files  [id => UploadedFile] của setting dạng ảnh / video
     */
    public function saveAll(array $values, array $files): void
    {
        foreach ($values as $id => $value) {
            $setting = $this->repository->find($id);

            // Không update file thông qua settings[]
            if (!$setting || $this->isFileSetting($setting)) {
                continue;
            }

            // Ô bí mật luôn hiện rỗng: để trống nghĩa là giữ giá trị cũ
            if ($setting->isSecret() && ($value === null || $value === '')) {
                continue;
            }

            $this->repository->updateValue($setting, $value);
        }

        foreach ($files as $id => $file) {
            if ($file && $file->isValid()) {
                $this->replaceFile($id, $file);
            }
        }

        FrontendLayoutComposer::forget();
    }

    /** Loại image / video: lưu file lên CDN, value là đường dẫn file */
    public function createSetting(array $data, ?UploadedFile $file): Model
    {
        if (in_array($data['type'] ?? null, ['image', 'video'], true)) {
            $data['value'] = $file ? $this->storeImage($file) : null;
        }

        unset($data['file']);

        $setting = parent::create($data);
        FrontendLayoutComposer::forget();

        return $setting;
    }

    public function delete(Model $model): void
    {
        if ($model->value && $this->isFileSetting($model)) {
            $this->deleteFile($model);
        }

        parent::delete($model);
        FrontendLayoutComposer::forget();
    }

    private function isFileSetting(Setting $setting): bool
    {
        return in_array($setting->type, ['image', 'video'], true) || $setting->isVideo();
    }

    private function replaceFile(int|string $id, UploadedFile $file): void
    {
        $setting = $this->repository->find($id);

        if (!$setting) {
            return;
        }

        $this->ensureValidFile($id, $file, $setting->isVideo());

        if ($setting->value) {
            $this->deleteFile($setting);
        }

        $this->repository->updateValue($setting, $this->storeImage($file));
    }

    private function ensureValidFile(int|string $id, UploadedFile $file, bool $isVideo): void
    {
        $allowed = $isVideo ? self::VIDEO_EXTENSIONS : self::IMAGE_EXTENSIONS;

        if (!in_array(strtolower($file->getClientOriginalExtension()), $allowed, true)) {
            throw ValidationException::withMessages([
                'uploads.' . $id => $isVideo
                    ? 'Video không đúng định dạng. Chỉ chấp nhận MP4, WebM hoặc OGG.'
                    : 'File ảnh không đúng định dạng.',
            ]);
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw ValidationException::withMessages([
                'uploads.' . $id => $isVideo ? 'Video không được vượt quá 2MB.' : 'Ảnh không được vượt quá 2MB.',
            ]);
        }
    }

    private function deleteFile(Setting $setting): void
    {
        try {
            cdn_delete($setting->value);
        } catch (\Exception $e) {
            Log::warning('Không thể xóa file setting', [
                'setting_id' => $setting->id,
                'path'       => $setting->value,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
