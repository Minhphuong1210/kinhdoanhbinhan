<?php

namespace App\Modules\Widget\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Widget\Models\Widget;
use App\Modules\Widget\Requests\WidgetRequest;
use App\Modules\Widget\Services\WidgetService;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    public function __construct(private readonly WidgetService $widgetService)
    {
    }

    public function index(Request $request)
    {
        $items = $this->widgetService->list();

        return view('Widget::Admin.index', compact('items'));
    }

    public function create()
    {
        return view('Widget::Admin.create');
    }

    public function store(WidgetRequest $request)
    {
        $this->widgetService->create($request->validated());

        return redirect()->route('admin.widgets.index')->with('success', 'Thêm thành công!');
    }

    public function edit(Widget $widget)
    {
        return view('Widget::Admin.edit', compact('widget'));
    }

    public function update(WidgetRequest $request, Widget $widget)
    {
        $this->widgetService->update($widget, $request->validated());

        return redirect()->route('admin.widgets.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Widget $widget)
    {
        $this->widgetService->delete($widget);

        return back()->with('success', 'Xóa thành công!');
    }
}
