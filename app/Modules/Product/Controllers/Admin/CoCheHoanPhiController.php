<?php

namespace App\Modules\Product\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoCheHoanPhi;
use App\Models\Product;
use Illuminate\Http\Request;

class CoCheHoanPhiController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()?->is_admin, 403, 'Bạn không có quyền thực hiện thao tác này.');

            return $next($request);
        });
    }

    public function index()
    {
        $items = CoCheHoanPhi::with('parent')->orderByDesc('id')->paginate(20);

        return view('Product::Admin.co-che-hoan-phi.index', compact('items'));
    }

    public function create()
    {
        $parents = CoCheHoanPhi::orderBy('name')->get();

        return view('Product::Admin.co-che-hoan-phi.create', compact('parents'));
    }

    public function store(Request $request)
    {
        CoCheHoanPhi::create($this->validated($request));

        return redirect()->route('admin.co-che-hoan-phi.index')
            ->with('success', 'Da tao co che hoan phi.');
    }

    public function edit(CoCheHoanPhi $coCheHoanPhi)
    {
        $parents = CoCheHoanPhi::where('id', '!=', $coCheHoanPhi->id)
            ->orderBy('name')
            ->get();

        return view('Product::Admin.co-che-hoan-phi.edit', compact('coCheHoanPhi', 'parents'));
    }

    public function update(Request $request, CoCheHoanPhi $coCheHoanPhi)
    {
        $coCheHoanPhi->update($this->validated($request, $coCheHoanPhi->id));

        return redirect()->route('admin.co-che-hoan-phi.index')
            ->with('success', 'Da cap nhat co che hoan phi.');
    }

    public function destroy(CoCheHoanPhi $coCheHoanPhi)
    {
        Product::where('co_che_hoan_phi_id', $coCheHoanPhi->id)->update([
            'co_che_hoan_phi_id' => null,
        ]);

        CoCheHoanPhi::where('parent_id', $coCheHoanPhi->id)->update([
            'parent_id' => null,
        ]);

        $coCheHoanPhi->delete();

        return back()->with('success', 'Da xoa co che hoan phi.');
    }

    private function validated(Request $request, ?int $ignoreParentId = null): array
    {
        $parentRule = 'nullable|exists:co_che_hoan_phi,id';
        if ($ignoreParentId) {
            $parentRule .= '|not_in:' . $ignoreParentId;
        }

        $data = $request->validate([
            'name' => 'required|string|max:225',
            'trang_hoat_dong' => 'required|in:combo,tu_chon',
            'tang_1' => 'nullable|numeric|min:0',
            'tang_2' => 'nullable|numeric|min:0',
            'tang_3' => 'nullable|numeric|min:0',
            'tang_4' => 'nullable|numeric|min:0',
            'tang_5' => 'nullable|numeric|min:0',
            'tang_6' => 'nullable|numeric|min:0',
            'parent_id' => $parentRule,
        ]);

        for ($i = 1; $i <= 6; $i++) {
            $key = 'tang_' . $i;
            $data[$key] = (float)($data[$key] ?? 0);
        }

        $data['parent_id'] = $data['parent_id'] ?? null;
        $data['trang_hoat_dong'] = $data['trang_hoat_dong'] ?? 'combo';

        return $data;
    }
}
