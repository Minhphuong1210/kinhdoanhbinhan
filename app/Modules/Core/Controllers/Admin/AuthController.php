<?php

namespace App\Modules\Core\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Permission\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        $logo = \App\Models\Setting::forWeb()->where('name', 'logo')->value('value');
        return view('Core::Admin.auth.login', compact('logo'));
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

        $login = trim($request->email);

        // Tìm user theo email hoặc SĐT
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);
        $user    = $isEmail
            ? User::where('email', $login)->first()
            : User::where('tel', $login)->first();

        if(!$user){
            return back()->withErrors(['email' => 'Không tìm thấy người  dùng.'])->withInput();
        }



        try {
            $check = Hash::check($request->password, $user->password);
        } catch (\RuntimeException $e) {
           
            $check = false;
        }

        if (!$check) {
            return back()->withErrors(['email' => 'Thông tin đăng nhập không đúng.'])->withInput();
        }
        if (!$user->active) {
            return back()->withErrors(['email' => 'Tài khoản của bạn đã bị khoá. Vui lòng liên hệ admin.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    public function showForgotPin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $logo = \App\Models\Setting::forWeb()->where('name', 'logo')->value('value');
        return view('Core::Admin.auth.forgot-pin', compact('logo'));
    }

    public function sendForgotPin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không đúng định dạng.',
            'email.exists'   => 'Không tìm thấy tài khoản với email này.',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();
        $pin = (string) random_int(100000, 999999);

        $user->forceFill(['pin' => $pin])->save();

        Mail::send('Core::Admin.emails.reset-pin', [
            'user' => $user,
            'pin'  => $pin,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject('Mã PIN mới của bạn');
        });

        return back()->with('success', 'Mã PIN mới đã được gửi về email của bạn.');
    }

    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        $logo = \App\Models\Setting::forWeb()->where('name', 'logo')->value('value');
        return view('Core::Admin.auth.forgot-password', compact('logo'));
    }

    public function sendForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không đúng định dạng.',
            'email.exists'   => 'Không tìm thấy tài khoản với email này.',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();
        $password = Str::random(10);

        $user->forceFill(['password' => Hash::make($password)])->save();

        Mail::send('Core::Admin.emails.reset-password', [
            'user'     => $user,
            'password' => $password,
        ], function ($message) use ($user) {
            $message->to($user->email, $user->name)
                ->subject('Mật khẩu mới của bạn');
        });

        return back()->with('success', 'Mật khẩu mới đã được gửi về email của bạn.');
    }

    public function showRegister(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        $provinces  = \App\Models\Provinces::orderBy('name')->get(['id','name']);
        $bankCities = \App\Models\ThanhPhoBanks::orderBy('name')->get(['id','name']);
        $refCode    = $request->input('ref');
        $logo       = \App\Models\Setting::forWeb()->where('name', 'logo')->value('value');

        $ngan_hangs = \App\Models\TongNganHang::query()
            ->orderBy('name')
            ->get();

        return view('Core::Admin.auth.register', compact('provinces', 'bankCities', 'refCode', 'logo','ngan_hangs'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',

            'email'     => 'required|string|email:rfc,dns|max:255|unique:users,email',

            'tel'       => [
                'required',
                'string',
                'max:20',
                'regex:/^(0|\+84)[0-9]{9}$/', // chuẩn số VN
                'unique:users,tel'
            ],

            'password'  => 'required|string|min:6|max:255|confirmed',

            'address'   => 'nullable|string|max:500',

            'ngan_hang' => 'nullable|string|max:255',

            'stk'       => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[0-9]+$/'
            ],

            'pin'       => [
                'nullable',
                'digits_between:4,6',
                'regex:/^[0-9]+$/'
            ],



        ], [
            // name
            'name.required' => 'Vui lòng nhập tên.',
            'name.max'      => 'Tên không được vượt quá 255 ký tự.',

            // email
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không đúng định dạng.',
            'email.unique'   => 'Email này đã được sử dụng.',

            // tel
            'tel.required' => 'Vui lòng nhập số điện thoại.',
            'tel.regex'    => 'Số điện thoại không đúng định dạng.',
            'tel.unique'   => 'Số điện thoại đã tồn tại.',

            // password
            'password.required'  => 'Vui lòng nhập mật khẩu.',
            'password.min'       => 'Mật khẩu tối thiểu 6 ký tự.',
            'password.confirmed' => 'Nhập lại mật khẩu không khớp.',

            // address
            'address.max' => 'Địa chỉ không quá 500 ký tự.',

            // ngân hàng
            'ngan_hang.max' => 'Tên ngân hàng không quá 255 ký tự.',

            // stk
            'stk.regex' => 'Số tài khoản chỉ được chứa số.',

            // pin
            'pin.digits_between' => 'PIN phải từ 4 đến 6 chữ số.',
            'pin.regex'          => 'PIN chỉ được chứa số.',

            // invite
            'invite_by.exists' => 'Mã giới thiệu không tồn tại.',
        ]);

        // Tìm người giới thiệu (theo ID, email hoặc SĐT)
        $parent = null;
        if ($request->filled('invite_by')) {
            $parent = User::where('id', $request->invite_by)
                ->orWhere('email', $request->invite_by)
                ->orWhere('tel', $request->invite_by)
                ->first();
        }
        if (!$parent) {
            return back()
                ->withInput()
                ->withErrors([
                    'invite_by' => 'Mã giới thiệu không tồn tại.'
                ]);
        }
        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'tel'               => $request->tel,
            'password'          => Hash::make($request->password),
            'province_id'       => $request->province_id,
            'district_id'       => $request->district_id,
            'ward_id'           => $request->ward_id,
            'address'           => $request->address,
            'cccd'              => $request->cccd,
            'ngan_hang'         => $request->ngan_hang,
            'ngan_hang_id'      => $request->tong_ngan_hang_id ?: null,
            'chi_nhanh_ngan_hang' => $request->ngan_hang_chi_nhanh_id,
            'stk'               => $request->stk,
            'chu_tk'            => $request->chu_tk,
            'pin'               => $request->filled('pin') ? $request->pin : null,
            'invite_by'         => $parent ? $parent->id : null,
            'active'            => 1,
            'vi_tien'           => 0,
            'bank_area_id'      =>$request->bank_area_id,
        ]);

        // Gán role 'user' mặc định
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $user->roles()->attach($userRole->id, ['admin_id' => $user->id, 'role_id' => $userRole->id]);
        }

        Auth::login($user);
        return redirect()->route('admin.dashboard')->with('success', 'Đăng ký thành công! Chào mừng ' . $user->name);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
