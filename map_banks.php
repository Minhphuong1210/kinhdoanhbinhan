<?php
/**
 * Script map tong_ngan_hang_id vào bảng ngan_hangs
 * Chạy: php artisan tinker --execute="require 'map_banks.php';"
 * Hoặc: php map_banks.php (từ thư mục gốc laravel)
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Lấy tất cả ngân hàng lớn
$tongNganHangs = DB::table('tong_ngan_hang')->get();

// Tạo map: keyword -> id
// Dùng ten_viet_tat vì ngắn gọn dễ match
$keywordMap = [];
foreach ($tongNganHangs as $tnh) {
    // Lấy phần trước dấu ( hoặc space để làm keyword
    $vietTat = trim($tnh->ten_viet_tat);
    // Tách nhiều keyword từ ten_viet_tat
    // vd: "BIDV (BID)" -> "BIDV", "BID"
    preg_match_all('/[A-Za-z][A-Za-z0-9\s]+/', $vietTat, $matches);
    foreach ($matches[0] as $kw) {
        $kw = trim($kw);
        if (strlen($kw) >= 3) {
            $keywordMap[strtolower($kw)] = $tnh->id;
        }
    }
    // Thêm từ đầu của name đầy đủ
    $words = explode(' ', $tnh->name);
    if (count($words) >= 2) {
        $shortName = implode(' ', array_slice($words, 0, 3));
        $keywordMap[strtolower($shortName)] = $tnh->id;
    }
}

// Thêm mapping thủ công cho các trường hợp đặc biệt
$manualMap = [
    'vietinbank'      => 237, // VietinBank (CTG)
    'viettinbank'     => 237,
    'viettin'         => 237,
    'vietcombank'     => 238, // Vietcombank (VCB)
    'vcb'             => 238,
    'bidv'            => 236, // BIDV (BID)
    'vpbank'          => 239, // VPBank
    'mbbank'          => 240, // MBBank
    'mb '             => 240,
    'techcombank'     => 241, // Techcombank
    'techcom'         => 241,
    'acb'             => 242, // ACB
    'shb'             => 243, // SHB
    'hdbank'          => 244, // HDBank
    'scb'             => 245, // SCB
    'sacombank'       => 246, // Sacombank
    'sacom'           => 246,
    'tpbank'          => 247, // TPBank
    'vib'             => 248, // VIB
    'msb'             => 249, // MSB
    'ocb'             => 251, // OCB
    'eximbank'        => 252, // Eximbank
    'exim'            => 252,
    'lpbank'          => 253, // LPBank
    'pvcombank'       => 254, // PVcombank
    'pvcom'           => 254,
    'bac a bank'      => 255, // Bac A Bank
    'abbank'          => 256, // ABBANK
    'dong a'          => 257, // Đông Á
    'bao viet'        => 258, // BaoViet
    'baoviet'         => 258,
    'nam a bank'      => 259, // Nam A Bank
    'nam a'           => 259,
    'vietbank'        => 260, // VietBank
    'viet a'          => 261, // Viet A
    'ncb'             => 262, // NCB
    'bvbank'          => 263, // BVBank
    'kienlong'        => 264, // Kienlongbank
    'pgbank'          => 266, // PGBank
    'ivb'             => 267, // IVB
    'vrb'             => 268, // VRB
    'nhcsxh'          => 269, // NHCSXH
    'vbsp'            => 269,
    'vdb'             => 270, // VDB
    'shinhan'         => 271, // Shinhan
    'hsbc'            => 272, // HSBC
    'standard chartered' => 273,
    'woori'           => 274, // Woori
    'cimb'            => 275, // CIMB
    'public bank'     => 276, // Public Bank
    'hong leong'      => 277, // Hong Leong
    'anz'             => 279, // ANZ
    'citibank'        => 280, // Citibank
    'citi bank'       => 280,
    'agribank'        => 283, // Agribank
    'agri'            => 283,
    'nnptnt'          => 283,
    'nong nghiep'     => 283,
];

// Lấy tất cả chi nhánh chưa được map
$chiNhanhs = DB::table('ngan_hangs')->whereNull('tong_ngan_hang_id')->get(['id', 'name']);

echo "Tổng chi nhánh cần map: " . $chiNhanhs->count() . "\n";

$matched = 0;
$unmatched = 0;
$unmatchedNames = [];

foreach ($chiNhanhs as $cn) {
    $nameLower = strtolower($cn->name);
    $foundId = null;

    // Thử manual map trước
    foreach ($manualMap as $kw => $id) {
        if (strpos($nameLower, $kw) !== false) {
            $foundId = $id;
            break;
        }
    }

    // Nếu không có manual, thử keyword map
    if (!$foundId) {
        foreach ($keywordMap as $kw => $id) {
            if (strpos($nameLower, $kw) !== false) {
                $foundId = $id;
                break;
            }
        }
    }

    if ($foundId) {
        DB::table('ngan_hangs')->where('id', $cn->id)->update(['tong_ngan_hang_id' => $foundId]);
        $matched++;
    } else {
        $unmatched++;
        $unmatchedNames[] = $cn->name;
    }
}

echo "✅ Đã map: {$matched}\n";
echo "❌ Chưa map: {$unmatched}\n";
if (!empty($unmatchedNames)) {
    echo "Danh sách chưa map:\n";
    foreach (array_slice($unmatchedNames, 0, 20) as $n) {
        echo "  - {$n}\n";
    }
}
echo "Xong!\n";
