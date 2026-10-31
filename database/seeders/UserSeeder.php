<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Tìm root user (admin) để làm tổ tiên
        $root = User::where('is_admin', 1)->first() ?? User::first();

        if (!$root) {
            $this->command->error('Chua co root user. Hay dang ky 1 tai khoan truoc.');
            return;
        }

        $names = [
            'Nguyen Van An','Tran Thi Bich','Le Van Cuong','Pham Thi Dung','Hoang Van Em',
            'Nguyen Thi Phuong','Vo Van Giau','Bui Thi Huong','Dang Van Ich','Ngo Thi Khanh',
            'Dinh Van Long','Truong Thi Mai','Ly Van Nam','Vu Thi Oanh','Thai Van Phuc',
            'Luu Thi Quyen','Cao Van Son','Duong Thi Tam','Phan Van Uyen','Ha Thi Van',
        ];

        $insertedIds = [];

        foreach ($names as $i => $name) {
            $slug = strtolower(iconv('UTF-8','ASCII//TRANSLIT', $name));
            $slug = preg_replace('/[^a-z0-9]/', '', str_replace(' ', '', $slug));
            $tel  = '09' . str_pad($i + 10000001, 8, '0', STR_PAD_LEFT);

            // Chọn cha: root cho 3 người đầu, các người sau lấy từ inserted
            if ($i < 3) {
                $parentId = $root->id;
            } else {
                $parentId = $insertedIds[array_rand(array_slice($insertedIds, 0, min(count($insertedIds), 6)))];
            }

            $parent    = User::find($parentId);
            $levelCha  = $parent->level ?? 0;
            $inviteBy  = (string) ($parent->id);

            $user = User::create([
                'name'          => $name,
                'email'         => $slug . '@demo.vn',
                'password'      => Hash::make('password'),
                'tel'           => $tel,
                'active'        => 1,
                'is_admin'      => 0,
                'parent_id'     => $parentId,
                'invite_by'     => $inviteBy,
                'level'         => $levelCha + 1,
                'ngay_tham_gia' => Carbon::now()->subDays(rand(1, 60)),
                'vi_tien'       => rand(0, 5000000),
                'vi_tien_status'=> ($i % 3 === 0) ? 1 : 0,
                'so_thanh_vien_gt' => 0,
            ]);

            // Ghi vào admin_hierarchy
            DB::table('admin_hierarchy')->insertOrIgnore(['ancestor_id' => $user->id, 'descendant_id' => $user->id, 'depth' => 0]);
            $depth     = 1;
            $currentId = $parentId;
            while ($currentId && $depth <= 6) {
                DB::table('admin_hierarchy')->insertOrIgnore(['ancestor_id' => $currentId, 'descendant_id' => $user->id, 'depth' => $depth]);
                $par       = User::find($currentId);
                if (!$par) break;
                $currentId = $par->parent_id;
                $depth++;
            }

            // Cập nhật so_thanh_vien_gt cho cha
            User::find($parentId)?->increment('so_thanh_vien_gt');

            $insertedIds[] = $user->id;

            // Tạo vài đơn hàng mẫu
            if ($i % 3 !== 0) {
                DB::table('orders')->insert([
                    'user_id'               => $user->id,
                    'ma_don_hang'           => 'DH' . strtoupper(substr(uniqid(), -6)),
                    'name'                  => 'Goi san pham mau',
                    'total_price'           => rand(100000, 1500000),
                    'total_price_hoan_tien' => rand(50000, 300000),
                    'status'                => 'completed',
                    'receive_user'          => $name,
                    'receive_phone'         => $tel,
                    'receive_address'       => '123 Demo',
                    'first_order'           => 1,
                    'created_at'            => Carbon::now()->subDays(rand(0, 10)),
                    'updated_at'            => Carbon::now(),
                ]);
            }
        }

        $this->command->info('Seeded ' . count($names) . ' users thanh cong!');
    }
}
