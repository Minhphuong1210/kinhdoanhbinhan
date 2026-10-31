<?php

namespace App\Modules\DangKyHTX\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DangKyXacNhanHtx;
use Illuminate\Http\Request;

class DangKyHTXController extends Controller
{
    public function index(Request $request)
    {
        $query = DangKyXacNhanHtx::query();
        if ($search = $request->search) {
            $query->where('full_name', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $items = $query->latest()->paginate(100)->withQueryString();
        return view('DangKyHTX::Admin.index', compact('items'));
    }

    public function show(DangKyXacNhanHtx $dangKyHTX)
    {
        return view('DangKyHTX::Admin.show', compact('dangKyHTX'));
    }

    public function update(Request $request, DangKyXacNhanHtx $dangKyHTX)
    {
        $data = $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $dangKyHTX->update($data);
        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function exportPdf(DangKyXacNhanHtx $dangKyHTX)
    {
        $pdf = app('dompdf.wrapper');

        $pdf->setPaper('a4');

        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
        ]);

        $pdf->loadView(
            'DangKyHTX::Admin.pdf',
            compact('dangKyHTX')
        );

        return $pdf->download(
            'chi-tiet-dang-ky-htx-' . $dangKyHTX->id . '.pdf'
        );
    }

    public function exportAllPdf()
    {
        $items = DangKyXacNhanHtx::all();

        $zipName = 'htx-all-' . time() . '.zip';
        $zipPath = storage_path('app/' . $zipName);

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {

            foreach ($items as $item) {

                $pdf = Pdf::loadView('DangKyHTX::Admin.pdf', [
                    'dangKyHTX' => $item
                ]);

                $zip->addFromString(
                    'htx-' . $item->id . '.pdf',
                    $pdf->output()
                );
            }

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }


}
