# Module Media

Thêm ảnh và lấy link ảnh, lưu ở thư mục local hoặc Cloudflare R2.

## Cấu trúc

```text
Media/
├── Config/media.php          cấu hình
├── Providers/                tự khai báo disk, helper, route, command
├── Controllers/Api/          nhận request, trả response
├── Requests/                 validate
├── Resources/                định dạng dữ liệu trả về
├── Services/MediaService.php nghiệp vụ
├── Repositories/             làm việc với kho lưu trữ
├── Console/                  lệnh chép ảnh sang R2
├── Routes/api.php
└── helpers.php               cdn(), cdn_upload(), cdn_delete(), cdn_base64()
```

## Cài sang dự án khác (Laravel 10 trở lên)

1. Chép thư mục `Media` vào `app/Modules/`.
2. Cài gói S3: `composer require league/flysystem-aws-s3-v3:"^3.0"`.
3. Thêm vào `providers` trong `config/app.php`:

   ```php
   App\Modules\Media\Providers\MediaServiceProvider::class,
   ```

4. Thêm các biến dưới đây vào `.env`, rồi chạy `php artisan config:clear`.

Yêu cầu PHP có extension `fileinfo`. API dùng `auth:sanctum`, dự án khác có thể đổi ở `routes.middleware` trong `Config/media.php`.

## Biến môi trường

```env
MEDIA_DRIVER=r2
MEDIA_MAX_KB=5120
MEDIA_LOCAL_ROOT=
MEDIA_LOCAL_URL=

R2_ACCESS_KEY_ID=
R2_SECRET_ACCESS_KEY=
R2_BUCKET=
R2_ENDPOINT=https://<account-id>.r2.cloudflarestorage.com
R2_PUBLIC_URL=https://<domain-anh>
```

`MEDIA_LOCAL_ROOT`, `MEDIA_LOCAL_URL` là nơi lưu ảnh khi `MEDIA_DRIVER=local`, đồng thời là nguồn khi chép ảnh sang R2.

## API

| Method | URL | Dữ liệu | Kết quả |
|---|---|---|---|
| POST | `/api/media` | `image` (file), `folder` (tùy chọn) | `data.path`, `data.url` |
| GET | `/api/media/url` | `path` | `data.path`, `data.url` |

Lưu `path` vào DB, dùng `url` để hiển thị.

## Dùng trong code

```php
$path = cdn_upload($request->file('image'), 'products'); // lưu vào DB
$url  = cdn($path);                                      // link hiển thị
cdn_delete($path);                                       // xoá
```

## Chuyển ảnh cũ sang R2

```bash
php artisan images:migrate-r2 --dry-run
php artisan images:migrate-r2 --rewrite-content
```

`--rewrite-content` đổi link ảnh cũ trong nội dung HTML. Danh sách bảng nằm ở `CONTENT_COLUMNS` trong command, dự án khác cần sửa cho đúng.
