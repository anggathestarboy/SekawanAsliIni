<?php

namespace App\Http\Controllers;

use App\Models\alat;
use App\Models\penyewaan;
use App\Models\penyewaanDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenyewaanController extends Controller
{
    public function index()
    {
        try {
            $penyewaan = penyewaan::with('details')->get();
            return response()->json([
                "success" => true,
                "message" => "berhasil ambil data penyewaan",
                "data" => $penyewaan
            ]);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "There error in Internal Server",
                "data" => null,
                "errors" => $exception->getMessage()
            ], 500);
        }
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                "penyewaan_pelanggan_id" => "required|exists:pelanggan,pelanggan_id",
                "penyewaan_tglsewa" => "required|date",
                "penyewaan_tglkembali" => "required|date|after_or_equal:penyewaan_tglsewa",
                "penyewaan_sttspembayaran" => "nullable|in:Lunas,Belum Dibayar,DP",
                "penyewaan_sttskembali" => "nullable|in:Sudah Kembali,Belum Kembali",
                "details" => "required|array|min:1",
                "details.*.penyewaan_detail_alat_id" => "required|exists:alat,alat_id",
                "details.*.penyewaan_detail_jumlah" => "required|integer|min:1",
            ]);

            DB::beginTransaction();

            $tglSewa = Carbon::parse($request->penyewaan_tglsewa);
            $tglKembali = Carbon::parse($request->penyewaan_tglkembali);
            $days = $tglSewa->diffInDays($tglKembali);
            if ($days == 0) $days = 1;

            $totalHarga = 0;

            $penyewaan = penyewaan::create([
                "penyewaan_pelanggan_id" => $request->penyewaan_pelanggan_id,
                "penyewaan_tglsewa" => $request->penyewaan_tglsewa,
                "penyewaan_tglkembali" => $request->penyewaan_tglkembali,
                "penyewaan_sttspembayaran" => $request->penyewaan_sttspembayaran ?? "Belum Dibayar",
                "penyewaan_sttskembali" => $request->penyewaan_sttskembali ?? "Belum Kembali",
                "penyewaan_totalharga" => 0 // Temporary
            ]);

            foreach ($request->details as $detail) {
                $alat = alat::lockForUpdate()->find($detail['penyewaan_detail_alat_id']);

                if ($alat->alat_stok < $detail['penyewaan_detail_jumlah']) {
                    throw new Exception("Stok alat " . $alat->alat_nama . " tidak mencukupi. Sisa stok: " . $alat->alat_stok);
                }

                $alat->alat_stok -= $detail['penyewaan_detail_jumlah'];
                $alat->save();

                $subharga = $alat->alat_hargaperhari * $detail['penyewaan_detail_jumlah'] * $days;
                $totalHarga += $subharga;

                penyewaanDetail::create([
                    "penyewaan_detail_penyewaan_id" => $penyewaan->penyewaan_id,
                    "penyewaan_detail_alat_id" => $alat->alat_id,
                    "penyewaan_detail_jumlah" => $detail['penyewaan_detail_jumlah'],
                    "penyewaan_detail_subharga" => $subharga
                ]);
            }

            $penyewaan->penyewaan_totalharga = $totalHarga;
            $penyewaan->save();

            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "berhasil membuat transaksi penyewaan",
                "data" => penyewaan::getDetail($penyewaan->penyewaan_id)
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
                "data" => null
            ], 500);
        }
    }

    public function show(int $penyewaan_id)
    {
        try {
            $penyewaan = penyewaan::getDetail($penyewaan_id);

            return response()->json([
                "success" => true,
                "message" => "berhasil ambil data penyewaan",
                "data" => $penyewaan
            ]);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "There error in Internal Server",
                "data" => null,
                "errors" => $exception->getMessage()
            ], 500);
        }
    }

    public function edit(penyewaan $penyewaan)
    {
    }

    public function update(Request $request, penyewaan $penyewaan)
    {
        try {
            $request->validate([
                "penyewaan_sttspembayaran" => "nullable|in:Lunas,Belum Dibayar,DP",
                "penyewaan_sttskembali" => "nullable|in:Sudah Kembali,Belum Kembali",
            ]);

            DB::beginTransaction();

            $oldStatus = $penyewaan->penyewaan_sttskembali;
            $newStatus = $request->penyewaan_sttskembali ?? $oldStatus;

            $penyewaan->update([
                "penyewaan_sttspembayaran" => $request->penyewaan_sttspembayaran ?? $penyewaan->penyewaan_sttspembayaran,
                "penyewaan_sttskembali" => $newStatus,
            ]);

            // Jika status berubah dari Belum Kembali menjadi Sudah Kembali, kembalikan stok
            if ($oldStatus != "Sudah Kembali" && $newStatus == "Sudah Kembali") {
                $details = penyewaanDetail::where("penyewaan_detail_penyewaan_id", $penyewaan->penyewaan_id)->get();
                foreach ($details as $detail) {
                    $alat = alat::lockForUpdate()->find($detail->penyewaan_detail_alat_id);
                    $alat->alat_stok += $detail->penyewaan_detail_jumlah;
                    $alat->save();
                }
            }
            
            // Jika direvert
            if ($oldStatus == "Sudah Kembali" && $newStatus == "Belum Kembali") {
                $details = penyewaanDetail::where("penyewaan_detail_penyewaan_id", $penyewaan->penyewaan_id)->get();
                foreach ($details as $detail) {
                    $alat = alat::lockForUpdate()->find($detail->penyewaan_detail_alat_id);
                    if ($alat->alat_stok < $detail->penyewaan_detail_jumlah) {
                        throw new Exception("Stok alat " . $alat->alat_nama . " tidak mencukupi untuk direvert.");
                    }
                    $alat->alat_stok -= $detail->penyewaan_detail_jumlah;
                    $alat->save();
                }
            }

            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "berhasil update status penyewaan",
                "data" => penyewaan::getDetail($penyewaan->penyewaan_id)
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
                "data" => null
            ], 500);
        }
    }

    public function destroy(penyewaan $penyewaan)
    {
        try {
            DB::beginTransaction();

            if ($penyewaan->penyewaan_sttskembali != "Sudah Kembali") {
                $details = penyewaanDetail::where("penyewaan_detail_penyewaan_id", $penyewaan->penyewaan_id)->get();
                foreach ($details as $detail) {
                    $alat = alat::lockForUpdate()->find($detail->penyewaan_detail_alat_id);
                    $alat->alat_stok += $detail->penyewaan_detail_jumlah;
                    $alat->save();
                }
            }

            $penyewaan->delete();

            DB::commit();

            return response()->json([
                "success" => true,
                "message" => "berhasil hapus penyewaan dan stok dikembalikan",
                "data" => $penyewaan
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => $exception->getMessage(),
                "data" => null
            ], 500);
        }
    }
}
