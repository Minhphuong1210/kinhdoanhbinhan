<?php

namespace App\Modules\Product\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoCheHoanPhi;
use App\Modules\Product\Requests\CoCheHoanPhiRequest;
use App\Modules\Product\Services\CoCheHoanPhiService;

class CoCheHoanPhiController extends Controller
{
    public function __construct(private readonly CoCheHoanPhiService $coCheService)
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()?->is_admin, 403, 'Bạn không có quyền thực hiện thao tác này.');

            return $next($request);
        });
    }

    public function index()
    {
        $items = $this->coCheService->list();

        return view('Product::Admin.co-che-hoan-phi.index', compact('items'));
    }

    public function create()
    {
        $parents = $this->coCheService->options();

        return view('Product::Admin.co-che-hoan-phi.create', compact('parents'));
    }

    public function store(CoCheHoanPhiRequest $request)
    {
        $this->coCheService->create($request->validated());

        return redirect()->route('admin.co-che-hoan-phi.index')->with('success', 'Da tao co che hoan phi.');
    }

    public function edit(CoCheHoanPhi $coCheHoanPhi)
    {
        $parents = $this->coCheService->options($coCheHoanPhi->id);

        return view('Product::Admin.co-che-hoan-phi.edit', compact('coCheHoanPhi', 'parents'));
    }

    public function update(CoCheHoanPhiRequest $request, CoCheHoanPhi $coCheHoanPhi)
    {
        $this->coCheService->update($coCheHoanPhi, $request->validated());

        return redirect()->route('admin.co-che-hoan-phi.index')->with('success', 'Da cap nhat co che hoan phi.');
    }

    public function destroy(CoCheHoanPhi $coCheHoanPhi)
    {
        $this->coCheService->delete($coCheHoanPhi);

        return back()->with('success', 'Da xoa co che hoan phi.');
    }
}
