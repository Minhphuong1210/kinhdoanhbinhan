<?php

namespace App\Modules\Widget\Services;

use App\Modules\Widget\Repositories\WidgetRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class WidgetService extends BaseService
{
    protected ?string $uploadFolder = 'widgets';

    public function __construct(WidgetRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin();
    }

    protected function prepare(array $data): array
    {
        $data['status'] = $this->flag($data, 'status');

        return $data;
    }
}
