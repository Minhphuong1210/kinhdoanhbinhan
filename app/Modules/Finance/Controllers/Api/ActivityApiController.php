<?php

namespace App\Modules\Finance\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LichSuGiaoDich;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['nullable', 'integer'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $items = LichSuGiaoDich::query()
            ->with([
                'sender:id,name,tel,email',
                'receiver:id,name,tel,email',
                'product:id,name,image',
            ])
            ->where('admin_id', $request->user()->id)
            ->when($data['type'] ?? null, fn ($query, int $type) => $query->where('type', $type))
            ->when($data['date_from'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($data['date_to'] ?? null, fn ($query, string $date) => $query->whereDate('created_at', '<=', $date))
            ->when($data['search'] ?? null, function ($query, string $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('note', 'like', "%{$search}%")
                        ->orWhereHas('sender', fn ($sender) => $sender->where('name', 'like', "%{$search}%")->orWhere('tel', 'like', "%{$search}%"))
                        ->orWhereHas('receiver', fn ($receiver) => $receiver->where('name', 'like', "%{$search}%")->orWhere('tel', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('id')
            ->paginate((int) ($data['per_page'] ?? 20));

        return $this->paginated($items, $items->getCollection()->map(fn (LichSuGiaoDich $item) => $this->activityPayload($item)));
    }

    private function activityPayload(LichSuGiaoDich $item): array
    {
        return [
            'id' => $item->id,
            'type' => (int) $item->type,
            'type_label' => $this->typeLabel((int) $item->type),
            'amount' => (float) ($item->amount ?? 0),
            'hoan_f1' => $item->hoan_f1 !== null ? (float) $item->hoan_f1 : null,
            'note' => $item->note,
            'surplus_before' => $item->surplus_before !== null ? (float) $item->surplus_before : null,
            'surplus' => $item->surplus !== null ? (float) $item->surplus : null,
            'surplus_vi_mua_before' => $item->surplus_vi_mua_befor !== null ? (float) $item->surplus_vi_mua_befor : null,
            'surplus_vi_mua' => $item->surplus_vi_mua !== null ? (float) $item->surplus_vi_mua : null,
            'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
            'sender' => $this->userPayload($item->sender),
            'receiver' => $this->userPayload($item->receiver),
            'product' => $item->product ? [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'image' => $this->assetUrl($item->product->image),
            ] : null,
        ];
    }

    private function typeLabel(int $type): string
    {
        return match ($type) {
            1 => 'Chi tieu',
            2 => 'Nhan hoa hong',
            3 => 'Rut tien',
            5 => 'Chuyen diem',
            default => 'Hoat dong',
        };
    }

    private function userPayload($user): ?array
    {
        if (!$user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'tel' => $user->tel,
            'email' => $user->email,
        ];
    }

    private function paginated($paginator, $items): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Thanh cong',
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ]);
    }

    private function assetUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        return function_exists('cdn') ? cdn($path) : asset($path);
    }
}
