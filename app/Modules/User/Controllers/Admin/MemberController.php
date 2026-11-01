<?php

namespace App\Modules\User\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\MemberService;
use App\Modules\User\Services\MemberTreeService;

class MemberController extends Controller
{
    public function __construct(
        private readonly MemberService $memberService,
        private readonly MemberTreeService $treeService
    ) {
    }

    public function index()
    {
        $user = auth()->user();
        $members = $this->memberService->directMembers($user);

        return view('Core::Admin.members.index', compact('members', 'user'));
    }

    /**
     * AJAX: lấy danh sách con trực tiếp của một user
     */
    public function children(int $userId)
    {
        abort_unless($this->memberService->canViewChildrenOf(auth()->user(), $userId), 403);

        return response()->json($this->memberService->childrenPayload($userId));
    }

    /**
     * Danh sách khách hàng trực tiếp — những người nhập mã giới thiệu của mình
     */
    public function myReferrals()
    {
        $user = auth()->user();
        $referrals = $this->memberService->referrals($user);

        return view('Core::Admin.members.referrals', compact('referrals', 'user'));
    }

    public function tree()
    {
        $user = auth()->user();
        $treeData = $this->treeService->webTree($user);

        return view('Core::Admin.tree.index', compact('treeData', 'user'));
    }
}
