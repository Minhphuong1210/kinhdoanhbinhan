<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Tạo tài khoản test đăng ký bằng mã giới thiệu của 1 user (giống luồng /api/register).
 * Tài khoản mới chưa mua hàng nên chưa vào cây (parent_id), hiện ở /api/khach-hang-truc-tiep.
 *
 * php artisan tao-tai-khoan-con 0987654321 --so-luong=5
 */
class TaoTaiKhoanConCommand extends Command
{
    protected $signature = 'tao-tai-khoan-con
                            {nguoi_gioi_thieu : SĐT, email hoặc ID của tài khoản cha}
                            {--so-luong=5 : Số tài khoản cần tạo}
                            {--mat-khau=123456 : Mật khẩu cho các tài khoản mới}';

    protected $description = 'Tạo tài khoản con (invite_by) cho 1 tài khoản để test';

    public function handle(): int
    {
        $inviteBy = $this->argument('nguoi_gioi_thieu');
        $soLuong  = max(1, min(50, (int) $this->option('so-luong')));

        $parent = User::where('tel', $inviteBy)
            ->orWhere('email', $inviteBy)
            ->orWhere('id', $inviteBy)
            ->first();

        if (!$parent) {
            $this->error("Không tìm thấy tài khoản {$inviteBy}.");

            return self::FAILURE;
        }

        $userRole = Role::where('name', 'user')->first();
        $created  = [];

        DB::transaction(function () use ($parent, $soLuong, $userRole, &$created) {
            for ($i = 1; $i <= $soLuong; $i++) {
                $tel = $this->taoSoDienThoai();

                $user = User::create([
                    'name'      => 'Test Con ' . $parent->id . ' ' . $i,
                    'email'     => 'test.con.' . $tel . '@example.com',
                    'tel'       => $tel,
                    'password'  => Hash::make((string) $this->option('mat-khau')),
                    'invite_by' => $parent->id,
                    'active'    => 1,
                    'vi_tien'   => 0,
                ]);

                if ($userRole) {
                    $user->roles()->attach($userRole->id, ['admin_id' => $user->id, 'role_id' => $userRole->id]);
                }

                $created[] = [$user->id, $user->name, $user->tel, $user->email];
            }
        });

        $this->info("Đã tạo {$soLuong} tài khoản con của {$parent->name} (ID {$parent->id}, {$parent->tel}):");
        $this->table(['ID', 'Tên', 'SĐT', 'Email'], $created);
        $this->line('Mật khẩu: ' . $this->option('mat-khau'));

        return self::SUCCESS;
    }

    /**
     * SĐT test dạng 0399xxxxxx, không trùng tài khoản đã có.
     */
    private function taoSoDienThoai(): string
    {
        do {
            $tel = '0399' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (User::where('tel', $tel)->exists());

        return $tel;
    }
}
