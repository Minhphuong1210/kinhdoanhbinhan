<?php

namespace App\Modules\ChinhSach\Services;

use App\Modules\ChinhSach\Repositories\ChinhSachRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ChinhSachService extends BaseService
{
    protected ?string $uploadFolder = 'chinh_sach';

    public function __construct(ChinhSachRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(?string $search): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($search);
    }

    protected function prepare(array $data): array
    {
        $data['status'] = $this->flag($data, 'status');

        return $data;
    }
}
