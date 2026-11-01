<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Chép ảnh từ thư mục CDN cũ sang Cloudflare R2 và đổi link ảnh trong nội dung CKEditor.
 * Đường dẫn file giữ nguyên nên các cột ảnh trong DB không phải đổi.
 */
class MigrateImagesToR2Command extends Command
{
    protected $signature = 'images:migrate-r2
        {--dry-run : Chỉ thống kê, không chép và không sửa DB}
        {--rewrite-content : Đổi link CDN cũ trong nội dung sang link R2}';

    protected $description = 'Chép ảnh từ CDN cũ sang Cloudflare R2';

    /** Các cột HTML có thể chứa link ảnh tuyệt đối */
    private const CONTENT_COLUMNS = [
        'products'     => ['content', 'instructions', 'description'],
        'posts'        => ['content', 'description'],
        'notification' => ['content'],
        'chinh_sach'   => ['content'],
        'widgets'      => ['content'],
        'thu_vien'     => ['content', 'intro'],
        'gioi_thieu'   => ['content'],
        'leaders'      => ['advantages', 'progress', 'achievements', 'contributions', 'goals', 'intro'],
    ];

    public function handle(): int
    {
        if (config('filesystems.disks.shared.driver') !== 's3') {
            $this->error('Chưa bật R2: đặt SHARED_DISK=r2 và điền các biến R2_* trong .env.');

            return self::FAILURE;
        }

        $this->copyFiles();

        if ($this->option('rewrite-content')) {
            $this->rewriteContent();
        }

        return self::SUCCESS;
    }

    private function copyFiles(): void
    {
        $source = Storage::disk('shared_local');
        $target = Storage::disk('shared');
        $files = $source->allFiles();
        $copied = 0;
        $skipped = 0;
        $failed = 0;

        $this->info('Tổng số file: ' . count($files));

        foreach ($files as $path) {
            if ($target->exists($path)) {
                $skipped++;
                continue;
            }

            if ($this->option('dry-run')) {
                $copied++;
                continue;
            }

            $stream = $source->readStream($path);

            if ($stream && $target->writeStream($path, $stream)) {
                $copied++;
            } else {
                $failed++;
                $this->warn('Lỗi: ' . $path);
            }

            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        $this->info("Chép: {$copied}, đã có: {$skipped}, lỗi: {$failed}");
    }

    private function rewriteContent(): void
    {
        $oldUrl = rtrim((string) config('filesystems.disks.shared_local.url'), '/') . '/';
        $newUrl = rtrim((string) config('filesystems.disks.shared.url'), '/') . '/';

        foreach (self::CONTENT_COLUMNS as $table => $columns) {
            foreach ($columns as $column) {
                $query = DB::table($table)->where($column, 'like', '%' . $oldUrl . '%');
                $count = $query->count();

                if ($count > 0 && !$this->option('dry-run')) {
                    $query->update([$column => DB::raw('REPLACE(`' . $column . '`, ' . DB::getPdo()->quote($oldUrl) . ', ' . DB::getPdo()->quote($newUrl) . ')')]);
                }

                if ($count > 0) {
                    $this->line("{$table}.{$column}: {$count} dòng");
                }
            }
        }
    }
}
