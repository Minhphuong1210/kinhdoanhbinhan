<?php

namespace App\Modules\Setting\Repositories;

use App\Models\Setting;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class SettingRepository extends BaseRepository
{
    protected string $model = Setting::class;

    public function groupedForWeb(): Collection
    {
        return $this->query()->forWeb()->orderBy('name')->get()->groupBy('type');
    }

    public function updateValue(Setting $setting, ?string $value): void
    {
        $setting->update(['value' => $value]);
    }
}
