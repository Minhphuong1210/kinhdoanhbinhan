<?php

namespace App\Modules\Media\Console;

use App\Modules\Media\Repositories\MediaRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ảnh trong DB không còn trên R2 thì đổi thành không có ảnh.
 * Mặc định chỉ thống kê, thêm --force mới ghi DB.
 */
class ClearMissingImagesCommand extends Command
{
    protected $signature = 'images:clear-missing
        {--force : Ghi thay đổi vào DB}
        {--allow-empty-bucket : Vẫn chạy khi bucket không có file nào}';

    protected $description = 'Đổi các ảnh không còn trên R2 thành không có ảnh';

    /** @var array<string, true> */
    private array $existing = [];

    private int $kept = 0;
    private int $cleared = 0;

    public function handle(MediaRepository $media): int
    {
        $this->existing = array_fill_keys($media->allFiles(), true);
        $this->info('Số file trên R2: ' . count($this->existing));

        // Sai key hoặc chưa chép ảnh thì bucket trống: chạy tiếp sẽ xoá sạch ảnh trong DB
        if ($this->existing === [] && !$this->option('allow-empty-bucket')) {
            $this->error('Bucket không có file nào, đã dừng để tránh xoá hết ảnh trong DB.');

            return self::FAILURE;
        }

        $this->clearImages();
        $this->clearGalleries();
        $this->cleanContent();
        $this->clearSettings();

        $this->info("Còn ảnh: {$this->kept}, không còn ảnh: {$this->cleared}");

        if (!$this->option('force')) {
            $this->warn('Mới chỉ thống kê. Sao lưu DB rồi thêm --force để ghi thay đổi.');
        }

        return self::SUCCESS;
    }

    private function clearImages(): void
    {
        foreach ($this->columns('image_columns') as [$table, $column]) {
            DB::table($table)
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->orderBy('id')
                ->select(['id', $column])
                ->chunk(500, function ($rows) use ($table, $column) {
                    foreach ($rows as $row) {
                        if (!$this->exists($row->$column)) {
                            $this->write($table, $row->id, [$column => null]);
                        }
                    }
                });
        }
    }

    private function clearGalleries(): void
    {
        foreach ($this->columns('gallery_columns') as [$table, $column]) {
            foreach (DB::table($table)->whereNotNull($column)->get(['id', $column]) as $row) {
                $paths = json_decode((string) $row->$column, true);

                if (!is_array($paths)) {
                    continue;
                }

                $remaining = array_values(array_filter($paths, fn ($path) => $this->exists($path)));

                if (count($remaining) !== count($paths)) {
                    $this->write($table, $row->id, [$column => $remaining ? json_encode($remaining) : null]);
                }
            }
        }
    }

    /** Ảnh trỏ tới link cũ: còn trên R2 thì đổi link, không còn thì bỏ thẻ img */
    private function cleanContent(): void
    {
        $oldUrls = config('media.cleanup.old_urls');

        foreach ($this->columns('content_columns') as [$table, $column]) {
            foreach ($oldUrls as $oldUrl) {
                DB::table($table)
                    ->where($column, 'like', '%' . $oldUrl . '/%')
                    ->orderBy('id')
                    ->select(['id', $column])
                    ->chunk(200, function ($rows) use ($table, $column, $oldUrl) {
                        foreach ($rows as $row) {
                            $html = $this->rewriteHtml((string) $row->$column, $oldUrl);

                            if ($html !== $row->$column) {
                                $this->write($table, $row->id, [$column => $html], false);
                            }
                        }
                    });
            }
        }
    }

    private function rewriteHtml(string $html, string $oldUrl): string
    {
        $base = preg_quote(rtrim($oldUrl, '/'), '#');

        return preg_replace_callback(
            '#<img\b[^>]*?src=["\']' . $base . '/([^"\']+)["\'][^>]*>#i',
            function (array $match) use ($oldUrl) {
                $path = rawurldecode($match[1]);

                if (!$this->exists($path)) {
                    $this->cleared++;

                    return '';
                }

                $this->kept++;

                return str_replace(rtrim($oldUrl, '/') . '/' . $match[1], cdn($path), $match[0]);
            },
            $html
        );
    }

    private function clearSettings(): void
    {
        if (!Schema::hasTable('setting')) {
            return;
        }

        $rows = DB::table('setting')
            ->whereIn('type', ['image', 'video'])
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->get(['id', 'value']);

        foreach ($rows as $row) {
            if (!$this->exists($row->value)) {
                $this->write('setting', $row->id, ['value' => null]);
            }
        }
    }

    /** Giá trị là link đầy đủ thì bỏ qua, chỉ kiểm tra đường dẫn trên R2 */
    private function exists(?string $path): bool
    {
        if (!$path || filter_var($path, FILTER_VALIDATE_URL)) {
            return true;
        }

        if (isset($this->existing[ltrim($path, '/')])) {
            $this->kept++;

            return true;
        }

        return false;
    }

    private function write(string $table, int $id, array $data, bool $count = true): void
    {
        if ($count) {
            $this->cleared++;
        }

        if ($this->option('force')) {
            DB::table($table)->where('id', $id)->update($data);
        }
    }

    /** @return array<int, array{0: string, 1: string}> */
    private function columns(string $key): array
    {
        $result = [];

        foreach (config('media.cleanup.' . $key) as $table => $columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    $result[] = [$table, $column];
                }
            }
        }

        return $result;
    }
}
