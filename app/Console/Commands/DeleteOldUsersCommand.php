<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Command;
use App\Modules\User\Services\UserTreeService;

class DeleteOldUsersCommand extends Command
{
    protected $signature = 'users:delete-old {days}';

    protected $description = 'Xóa user theo số ngày không hoạt động';

    public function handle()
    {
        $days = (int) $this->argument('days');

        $date = Carbon::now()->subDays($days);

        $this->info(
            "Đang tìm user có ngay_mua_don_cuoi <= {$date->format('Y-m-d')}"
        );

        $deleted = 0;

        User::query()
            ->whereDate('ngay_mua_don_cuoi', '<=', $date)
            ->whereNotIn('id', [1, 22205, 22206])
            ->orderByDesc('id')
            ->chunkByIdDesc(100, function ($users) use (&$deleted) {

                foreach ($users as $user) {

                    try {

                        app(UserTreeService::class)
                            ->deleteAndRebuildTree($user);

                        $deleted++;

                        $this->line(
                            "✓ Đã xóa ID {$user->id}"
                        );

                    } catch (\Throwable $e) {

                        $this->error(
                            "✗ ID {$user->id}: {$e->getMessage()}"
                        );
                    }
                }
            });

        $this->newLine();

        $this->info(
            "Hoàn tất. Đã xóa {$deleted} tài khoản."
        );

        return self::SUCCESS;
    }
}