<?php

namespace App\Http\Controllers;

use App\Models\alat;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $alat = Cache::remember('alat', 60 * 60 * 24, function () {
                return alat::all()->toArray();
            });

            if ($alat === null) {
                return response()->json([
                    "success" => true,
                    "message" => "berhasil ambil data alat",
                    "data" => null
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "berhasil ambil data alat",
                "data" => $alat
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                "alat_kategori_id" => "required|exists:kategori,kategori_id",
                "alat_nama" => "required",
                "alat_deskripsi" => "required",
                "alat_hargaperhari" => "required|numeric",
                "alat_stok" => "required|numeric",
            ]);

            $alat = alat::create($request->all());
            
            Cache::forget('alat');
            
            return response()->json([
                "success" => true,
                "message" => "berhasil buat alat",
                "data" => $alat
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

    /**
     * Display the specified resource.
     */
    public function show(int $alat_id)
    {
        try {
            $alat = Cache::remember('alat_' . $alat_id, 60 * 60 * 24, function () use ($alat_id) {
                $detail = alat::getDetail($alat_id);
                return $detail ? $detail->toArray() : null;
            });

            return response()->json([
                "success" => true,
                "message" => "berhasil ambil data alat",
                "data" => $alat
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(alat $alat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, alat $alat)
    {
        try {
            $request->validate([
                "alat_kategori_id" => "required|exists:kategori,kategori_id",
                "alat_nama" => "required",
                "alat_deskripsi" => "required",
                "alat_hargaperhari" => "required|numeric",
                "alat_stok" => "required|numeric",
            ]);

            $alat->update($request->all());

            Cache::forget('alat');
            Cache::forget('alat_' . $alat->alat_id);

            return response()->json([
                "success" => true,
                "message" => "berhasil update alat",
                "data" => $alat
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(alat $alat)
    {
        try {
            $alat->delete();
            Cache::forget('alat_' . $alat->alat_id);
            Cache::forget('alat');

            return response()->json([
                "success" => true,
                "message" => "berhasil di hapus",
                "data" => $alat
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
}
