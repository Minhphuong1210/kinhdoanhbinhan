<?php

namespace App\Modules\Media\Providers;

use App\Modules\Media\Console\MigrateImagesToR2Command;
use Illuminate\Support\ServiceProvider;

/**
 * Module tự khai báo config, disk, helper, route và command.
 * Mang sang dự án khác: chép thư mục Media và thêm provider này vào config/app.php.
 */
class MediaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once __DIR__ . '/../helpers.php';

        $this->mergeConfigFrom(__DIR__ . '/../Config/media.php', 'media');

        $local = [
            'driver'     => 'local',
            'root'       => config('media.local.root'),
            'url'        => config('media.local.url'),
            'visibility' => 'public',
            'throw'      => false,
        ];

        $r2 = [
            'driver'                  => 's3',
            'key'                     => config('media.r2.key'),
            'secret'                  => config('media.r2.secret'),
            'region'                  => 'auto',
            'bucket'                  => config('media.r2.bucket'),
            'endpoint'                => config('media.r2.endpoint'),
            'url'                     => config('media.r2.public_url'),
            'use_path_style_endpoint' => false,
            'throw'                   => false,
        ];

        config([
            'filesystems.disks.media'        => config('media.driver') === 'r2' ? $r2 : $local,
            'filesystems.disks.media_legacy' => $local,
        ]);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->commands([MigrateImagesToR2Command::class]);
        }
    }
}
