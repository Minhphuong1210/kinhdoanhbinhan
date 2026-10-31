<?php

namespace App\Modules\Widget\Repositories;

use App\Modules\Widget\Models\Widget;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WidgetRepository extends BaseRepository
{
    protected string $model = Widget::class;

    public function paginateForAdmin(): LengthAwarePaginator
    {
        return $this->paginateWithSearch($this->query()->orderBy('order_no'), null, 'title');
    }
}
