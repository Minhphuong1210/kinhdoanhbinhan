<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // ========================
        // 1. TẠO PERMISSIONS
        // ========================
        $permissions = [
            // Category
            ['name'=>'category.view',   'display_name'=>'Xem danh mục sản phẩm'],
            ['name'=>'category.create', 'display_name'=>'Tạo danh mục sản phẩm'],
            ['name'=>'category.edit',   'display_name'=>'Sửa danh mục sản phẩm'],
            ['name'=>'category.delete', 'display_name'=>'Xóa danh mục sản phẩm'],
            // Product
            ['name'=>'product.view',    'display_name'=>'Xem sản phẩm'],
            ['name'=>'product.create',  'display_name'=>'Tạo sản phẩm'],
            ['name'=>'product.edit',    'display_name'=>'Sửa sản phẩm'],
            ['name'=>'product.delete',  'display_name'=>'Xóa sản phẩm'],
            // Order
            ['name'=>'order.view',      'display_name'=>'Xem đơn hàng'],
            ['name'=>'order.edit',      'display_name'=>'Cập nhật trạng thái đơn hàng'],
            ['name'=>'order.delete',    'display_name'=>'Xóa đơn hàng'],
            // Post & Category Post
            ['name'=>'post.view',       'display_name'=>'Xem bài viết'],
            ['name'=>'post.create',     'display_name'=>'Tạo bài viết'],
            ['name'=>'post.edit',       'display_name'=>'Sửa bài viết'],
            ['name'=>'post.delete',     'display_name'=>'Xóa bài viết'],
            // User
            ['name'=>'user.view',       'display_name'=>'Xem thành viên'],
            ['name'=>'user.edit',       'display_name'=>'Sửa thành viên'],
            ['name'=>'user.delete',     'display_name'=>'Xóa thành viên'],
            // Finance
            ['name'=>'finance.view',    'display_name'=>'Xem tài chính'],
            ['name'=>'finance.approve', 'display_name'=>'Duyệt rút tiền'],
            ['name'=>'finance.transfer','display_name'=>'Chuyển điểm nội bộ'],
            // Settings
            ['name'=>'setting.view',    'display_name'=>'Xem cài đặt'],
            ['name'=>'setting.edit',    'display_name'=>'Sửa cài đặt'],
            // RBAC
            ['name'=>'rbac.manage',     'display_name'=>'Quản lý phân quyền'],
            // Leader
            ['name'=>'leader.manage',   'display_name'=>'Quản lý lãnh đạo'],
            // Content
            ['name'=>'content.manage',  'display_name'=>'Quản lý nội dung (CS, GT, Widget)'],
            // HTX
            ['name'=>'htx.manage',      'display_name'=>'Quản lý đăng ký HTX'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], array_merge($p, ['status' => 1]));
        }

        // ========================
        // 2. TẠO ROLES
        // ========================

        // Super Admin - có tất cả quyền
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin'],
            ['display_name' => 'Super Admin', 'description' => 'Toàn quyền hệ thống']
        );
        $superAdmin->permissions()->sync(Permission::pluck('id'));

        // Manager - quản lý nội dung và đơn hàng
        $manager = Role::firstOrCreate(
            ['name' => 'manager'],
            ['display_name' => 'Quản lý', 'description' => 'Quản lý sản phẩm, đơn hàng, nội dung']
        );
        $managerPerms = Permission::whereIn('name', [
            'category.view','category.create','category.edit',
            'product.view','product.create','product.edit',
            'order.view','order.edit',
            'post.view','post.create','post.edit',
            'user.view','content.manage',
        ])->pluck('id');
        $manager->permissions()->sync($managerPerms);

        // Editor - chỉ quản lý nội dung bài viết
        $editor = Role::firstOrCreate(
            ['name' => 'editor'],
            ['display_name' => 'Biên tập viên', 'description' => 'Quản lý bài viết']
        );
        $editorPerms = Permission::whereIn('name', [
            'post.view','post.create','post.edit','post.delete',
            'category.view','content.manage',
        ])->pluck('id');
        $editor->permissions()->sync($editorPerms);

        // Finance - quản lý tài chính
        $finance = Role::firstOrCreate(
            ['name' => 'finance'],
            ['display_name' => 'Tài chính', 'description' => 'Quản lý tài chính và rút tiền']
        );
        $financePerms = Permission::whereIn('name', [
            'finance.view','finance.approve','finance.transfer','user.view','order.view',
        ])->pluck('id');
        $finance->permissions()->sync($financePerms);

        // User thường - chỉ xem thống kê dashboard
        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            ['display_name' => 'Thành viên', 'description' => 'Truy cập admin hạn chế — chỉ xem Dashboard']
        );
        // Không gán permission → $can() sẽ trả false tất cả → chỉ thấy Dashboard
        $userRole->permissions()->sync([]);

        // ========================
        // 3. TẠO SUPER ADMIN USER
        // ========================
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name'     => 'Super Administrator',
                'password' => Hash::make('Admin@123'),
                'is_admin' => 1,
                'active'   => 1,
            ]
        );

        // Gán role super-admin
        if (!$adminUser->roles()->where('name','super-admin')->exists()) {
            $adminUser->roles()->attach($superAdmin->id);
        }

        $this->command->info("✅ RBAC Seeder hoàn tất!");
        $this->command->info("   Admin: admin@admin.com / Admin@123");
        $this->command->info("   Roles: super-admin, manager, editor, finance");
        $this->command->info("   Permissions: ".Permission::count()." permissions");
    }
}
