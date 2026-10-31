<?php

namespace App\Modules\Leader\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Leader\Models\Leader;
use App\Modules\Leader\Requests\LeaderRequest;
use App\Modules\Leader\Services\LeaderService;
use Illuminate\Http\Request;

class LeaderController extends Controller
{
    public function __construct(private readonly LeaderService $leaderService)
    {
    }

    public function index(Request $request)
    {
        $items = $this->leaderService->list($request->search);

        return view('Leader::Admin.index', compact('items'));
    }

    public function create()
    {
        return view('Leader::Admin.create');
    }

    public function store(LeaderRequest $request)
    {
        $this->leaderService->create($request->validated());

        return redirect()->route('admin.leaders.index')->with('success', 'Thêm lãnh đạo thành công!');
    }

    public function edit(Leader $leader)
    {
        return view('Leader::Admin.edit', compact('leader'));
    }

    public function update(LeaderRequest $request, Leader $leader)
    {
        $this->leaderService->update($leader, $request->validated());

        return redirect()->route('admin.leaders.index')->with('success', 'Cập nhật lãnh đạo thành công!');
    }

    public function destroy(Leader $leader)
    {
        $this->leaderService->delete($leader);

        return back()->with('success', 'Xóa thành công!');
    }
}
