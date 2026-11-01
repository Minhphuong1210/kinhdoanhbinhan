<?php

namespace App\Modules\Auth\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Location\Services\LocationService;
use App\Modules\Setting\Repositories\SettingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** Trả cùng một thông báo: không tiết lộ email có tồn tại hay không */
    private const FORGOT_PIN_SENT = 'Nếu email tồn tại, mã PIN mới đã được gửi về email của bạn.';
    private const FORGOT_PASSWORD_SENT = 'Nếu email tồn tại, mật khẩu mới đã được gửi về email của bạn.';

    public function __construct(
        private readonly AuthService $authService,
        private readonly LocationService $locations,
        private readonly SettingRepository $settings
    ) {
    }

    public function showLogin()
    {
        return $this->guestView('Core::Admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ], [
            'email.required'    => 'Vui lòng nhập email hoặc số điện thoại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $user = $this->authService->findByCredentials((string) $request->email, (string) $request->password);

        if (!$user) {
            return back()->withErrors(['email' => 'Thông tin đăng nhập không đúng.'])->withInput($request->except('password'));
        }

        if (!$user->active) {
            return back()->withErrors(['email' => 'Tài khoản của bạn đã bị khoá. Vui lòng liên hệ admin.'])->withInput($request->except('password'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function showForgotPin()
    {
        return $this->guestView('Core::Admin.auth.forgot-pin');
    }

    public function sendForgotPin(Request $request)
    {
        $data = $request->validate(['email' => 'required|email'], $this->emailMessages());

        $this->authService->resetPin($data['email']);

        return back()->with('success', self::FORGOT_PIN_SENT);
    }

    public function showForgotPassword()
    {
        return $this->guestView('Core::Admin.auth.forgot-password');
    }

    public function sendForgotPassword(Request $request)
    {
        $data = $request->validate(['email' => 'required|email'], $this->emailMessages());

        $this->authService->resetPassword($data['email']);

        return back()->with('success', self::FORGOT_PASSWORD_SENT);
    }

    public function showRegister(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $provinces  = $this->locations->provinces(['id', 'name']);
        $bankCities = $this->locations->bankAreas();
        $refCode    = $request->input('ref');
        $logo       = $this->logo();
        $ngan_hangs = $this->locations->banks();

        return view('Core::Admin.auth.register', compact('provinces', 'bankCities', 'refCode', 'logo', 'ngan_hangs'));
    }

    public function register(RegisterRequest $request)
    {
        $referrer = $this->authService->findReferrer($request->invite_by);

        if (!$referrer) {
            return back()
                ->withInput($request->except('password', 'password_confirmation', 'pin'))
                ->withErrors(['invite_by' => 'Mã giới thiệu không tồn tại.']);
        }

        $user = $this->authService->createAccount([
            'name'                => $request->name,
            'email'               => $request->email,
            'tel'                 => $request->tel,
            'password'            => Hash::make($request->password),
            'province_id'         => $request->province_id,
            'district_id'         => $request->district_id,
            'ward_id'             => $request->ward_id,
            'address'             => $request->address,
            'cccd'                => $request->cccd,
            'ngan_hang'           => $request->ngan_hang,
            'ngan_hang_id'        => $request->tong_ngan_hang_id ?: null,
            'chi_nhanh_ngan_hang' => $request->ngan_hang_chi_nhanh_id,
            'stk'                 => $request->stk,
            'chu_tk'              => $request->chu_tk,
            'pin'                 => $request->filled('pin') ? $request->pin : null,
            'bank_area_id'        => $request->bank_area_id,
        ], $referrer);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')->with('success', 'Đăng ký thành công! Chào mừng ' . $user->name);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /** Admin đăng nhập vào tài khoản thành viên để hỗ trợ */
    public function loginAsUser(Request $request, User $user)
    {
        if ($request->user()->is_admin != 1) {
            return redirect()->route('admin.users.index');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    private function guestView(string $view)
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view($view, ['logo' => $this->logo()]);
    }

    private function logo(): ?string
    {
        return $this->settings->valueOf('logo');
    }

    private function emailMessages(): array
    {
        return [
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không đúng định dạng.',
        ];
    }
}
