<?php

namespace App\Modules\Notifycation\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Modules\Notifycation\Requests\SendBroadcastRequest;
use App\Modules\Notifycation\Services\BroadcastNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BroadcastNotificationController extends Controller
{
    public function __construct(private readonly BroadcastNotificationService $broadcastService)
    {
    }

    public function index(Request $request)
    {
        $items   = $this->broadcastService->list($request->only(['vi_tri', 'search']));
        $targets = BroadcastNotificationService::TARGETS;

        return view('Notifycation::Admin.index', compact('items', 'targets'));
    }

    public function create()
    {
        $targets = BroadcastNotificationService::TARGETS;

        return view('Notifycation::Admin.create', compact('targets'));
    }

    public function store(SendBroadcastRequest $request)
    {
        $data = $request->validated();

        $this->broadcastService->send($data['title'], $data['content'], $data['vi_tri'], $request->file('image'));

        return redirect()->route('admin.thong-bao.index')
            ->with('success', 'Đã gửi thông báo tới ' . BroadcastNotificationService::TARGETS[$data['vi_tri']] . '.');
    }

    public function edit(Notification $thongBao)
    {
        $this->ensureBroadcast($thongBao);

        $targets = BroadcastNotificationService::TARGETS;

        return view('Notifycation::Admin.edit', ['item' => $thongBao, 'targets' => $targets]);
    }

    public function update(SendBroadcastRequest $request, Notification $thongBao)
    {
        $this->ensureBroadcast($thongBao);

        $data = $request->validated();

        $this->broadcastService->update($thongBao, $data['title'], $data['content'], $data['vi_tri'], $request->file('image'));

        return redirect()->route('admin.thong-bao.index')
            ->with('success', 'Đã cập nhật thông báo. Nội dung mới hiện trong app; thông báo đẩy đã gửi thì không gửi lại.');
    }

    public function destroy(Notification $thongBao)
    {
        $this->ensureBroadcast($thongBao);

        $this->broadcastService->delete($thongBao);

        return back()->with('success', 'Đã xoá thông báo. Thông báo đã đẩy tới điện thoại thì không thu hồi được.');
    }

    /**
     * POST /admin/thong-bao/upload-image — upload ảnh chèn trong CKEditor
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'upload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $url = $this->broadcastService->uploadEditorImage($request->file('upload'));

        return response()->json([
            'uploaded' => 1,
            'fileName' => basename($url),
            'url'      => $url,
        ]);
    }

    private function ensureBroadcast(Notification $notification): void
    {
        // Chỉ quản lý thông báo chung, không đụng thông báo riêng của từng user
        abort_unless($notification->target_type === Notification::TARGET_ALL, 404);
    }
}
