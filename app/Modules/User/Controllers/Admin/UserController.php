<?php

namespace App\Modules\User\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Districts;
use App\Models\Provinces;
use App\Modules\Permission\Models\Role;
use App\Models\User;
use App\Models\Wards;
use App\Exports\MembersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\UserTreeService;
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user.view')->only(['index', 'export', 'searchUsers']);
        $this->middleware('permission:user.edit')->only(['create', 'store', 'edit', 'update', 'AdminLogin']);
        $this->middleware('permission:user.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = User::query()
            ->with('dangKyXacNhanHtx');
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('tel', 'like', "%$search%");
            });
        }
        if ($request->filled('active'))   { $query->where('active', $request->active); }
        if ($request->filled('is_admin')) { $query->where('is_admin', $request->is_admin); }

//        if ($request->filled('tu_ngay')) {
//            $query->whereDate('created_at', '>=', $request->tu_ngay);
//        }
//        if ($request->filled('den_ngay')) {
//            $query->whereDate('created_at', '<=', $request->den_ngay);
//        }

        if ($request->filled('tu_ngay')) {
            $query->where(
                'created_at',
                '>=',
                Carbon::parse($request->tu_ngay)->startOfDay()
            );
        }

        if ($request->filled('den_ngay')) {
            $query->where(
                'created_at',
                '<=',
                Carbon::parse($request->den_ngay)->endOfDay()
            );
        }


//        dd($request->tu_ngay);

        $users = $query->latest()->paginate(20)->withQueryString();
        return view('User::Admin.index', compact('users'));
    }

    public function create()
    {
        $roles     = Role::orderBy('name')->get();
        $parents   = User::select('id', 'name', 'email')->orderBy('name')->get();
        $provinces = Provinces::orderBy('name')->get();
        return view('User::Admin.create', compact('roles', 'parents', 'provinces'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|max:255',
//            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'tel'                   => 'nullable|max:20|unique:users,tel',
            'parent_id'             => 'nullable',
            'invite_by'             => 'nullable',
            'pin'                   => 'nullable|max:20',
            'ngan_hang'             => 'nullable|max:255',
            'stk'                   => 'nullable|max:50',
            'vi_tien'               => 'nullable|numeric|min:0',
            'province_id'           => 'nullable',
            'district_id'           => 'nullable',
            'ward_id'               => 'nullable',
            'address'               => 'nullable|max:500',
            'avatar'                => 'nullable|image|max:2048',
        ]);

        unset($data['password_confirmation']);
        $data['password'] = Hash::make($data['password']);
        $data['active']   = $request->boolean('active', true);
        $data['is_admin'] = $request->boolean('is_admin');
        $data['vi_tien']  = $data['vi_tien'] ?? 0;


        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'avatars');
        }
        $user = User::create($data);
        if ($request->filled('roles')) {
            $user->roles()->sync($request->roles);
        }
        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản thành công!');
    }

    public function edit(User $user)
    {
        $roles     = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('id')->toArray();
        $parents   = User::select('id', 'name', 'email')->where('id', '!=', $user->id)->orderBy('name')->get();
        $provinces = Provinces::orderBy('name')->get();

        // Load district/ward of current user for pre-select
        $districts = $user->province_id
            ? Districts::where('province_id', $user->province_id)->orderBy('name')->get()
            : collect();
        $wards = $user->district_id
            ? Wards::where('district_id', $user->district_id)->orderBy('name')->get()
            : collect();

        return view('User::Admin.edit', compact('user', 'roles', 'userRoles', 'provinces','parents', 'districts', 'wards'));
    }

    public function update(Request $request, User $user)
    {

//        dd($request->all());

        $data = $request->validate([
            'name'        => 'required|max:255',
//            'email'       => 'required|email|unique:users,email,'.$user->id,
            'tel'         => 'nullable|max:20|unique:users,tel,'.$user->id,
            'parent_id'   => 'nullable',
            'invite_by'   => 'nullable',
            'pin'         => 'nullable',
            'ngan_hang'   => 'nullable|max:255',
            'stk'         => 'nullable|max:50',
            'vi_tien'     => 'nullable|numeric|min:0',
            'province_id' => 'nullable',
            'district_id' => 'nullable',
            'ward_id'     => 'nullable',
            'address'     => 'nullable|max:500',
            'avatar'      => 'nullable|image|max:2048',
        ]);

//        dd($data);

        $data['active']   = $request->boolean('active');
        $data['is_admin'] = $request->boolean('is_admin');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        if ($request->hasFile('image')) {
            $data['image'] = cdn_upload($request->file('image'), 'avatars');
        }
        $user->update($data);
        $user->roles()->sync($request->roles ?? []);
        return redirect()->route('admin.users.index')->with('success', 'Cập nhật thành viên thành công!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể xóa tài khoản đang đăng nhập!');
        }



        app(UserTreeService::class)
            ->deleteAndRebuildTree($user);


        return back()->with('success', 'Xóa thành viên thành công!');
    }

    public function export(Request $request)
    {
        $tuNgay  = $request->tu_ngay;
        $denNgay = $request->den_ngay;

        $filename = 'danh-sach-thanh-vien';
        if ($tuNgay) $filename .= '-' . $tuNgay;
        if ($denNgay) $filename .= '-' . $denNgay;
        $filename .= '.xlsx';

        return Excel::download(new MembersExport($tuNgay, $denNgay), $filename);
    }

    public function searchUsers(Request $request)
    {
        $q = $request->q;

        $users = User::when($q, function ($query) use ($q) {
            $query->where('name', 'like', "%$q%")
                ->orWhere('email', 'like', "%$q%")
                ->orWhere('tel', 'like', "%$q%");
        })
            ->select('id', 'name', 'email', 'tel')
            ->limit(20)
            ->get();

        return response()->json($users);
    }

    public function AdminLogin(int $id){
        if(auth()->user()->is_admin != 1){
            return redirect()->route('admin.users.index');
        }

        $user = User::where('id', $id)->first();


//        dd($user);

        Auth::login($user);

        return redirect()->route('admin.dashboard');


    }


}
