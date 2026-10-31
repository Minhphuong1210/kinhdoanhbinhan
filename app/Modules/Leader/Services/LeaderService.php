<?php

namespace App\Modules\Leader\Services;

use App\Modules\Leader\Repositories\LeaderRepository;
use App\Modules\Shared\Services\BaseService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

class LeaderService extends BaseService
{
    protected ?string $uploadFolder = 'leaders';

    public function __construct(LeaderRepository $repository)
    {
        parent::__construct($repository);
    }

    public function list(?string $search): LengthAwarePaginator
    {
        return $this->repository->paginateForAdmin($search);
    }

    public function create(array $data): Model
    {
        // Thêm mới: không gửi status thì mặc định hiển thị
        $data['status'] = $data['status'] ?? true;

        return parent::create($data);
    }

    protected function prepare(array $data): array
    {
        $data['status'] = $this->flag($data, 'status');
        $data['ambassador'] = $this->flag($data, 'ambassador');
        $data['is_ambassador'] = $this->flag($data, 'is_ambassador');
        $data['gender'] = $data['gender'] ?? 'male';

        return $data;
    }
}
