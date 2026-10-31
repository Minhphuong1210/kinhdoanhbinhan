<?php

namespace App\Modules\Product\Repositories;

use App\Models\Product;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository extends BaseRepository
{
    protected string $model = Product::class;

    public function activeOrderedByName(): Collection
    {
        return $this->query()->where('active', 1)->orderBy('name')->get();
    }
}
