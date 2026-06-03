<?php

namespace App\Http\Controllers;

use App\Models\pelanggan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $pelanggan = Cache::remember('pelanggan', 60 * 60 * 24, function () {
                return pelanggan::all()->toArray();
            });

            if ($pelanggan === null) {
                return response()->json([
                    "success" => true,
                    "message" => "berhasil ambil data pelanggan",
                    "data" => null
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "berhasil ambil data pelanggan",
                "data" => $pelanggan
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
                "pelanggan_nama" => "required",
                "pelanggan_alamat" => "required",
                "pelanggan_notelp" => "required|max:13",
                "pelanggan_email" => "required|email",
            ]);

            $pelanggan = pelanggan::create($request->all());
            
            Cache::forget('pelanggan');
            
            return response()->json([
                "success" => true,
                "message" => "berhasil buat pelanggan",
                "data" => $pelanggan
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
    public function show(int $pelanggan_id)
    {
        try {
            $pelanggan = Cache::remember('pelanggan_' . $pelanggan_id, 60 * 60 * 24, function () use ($pelanggan_id) {
                $detail = pelanggan::getDetail($pelanggan_id);
                return $detail ? $detail->toArray() : null;
            });

            return response()->json([
                "success" => true,
                "message" => "berhasil ambil data pelanggan",
                "data" => $pelanggan
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
    public function edit(pelanggan $pelanggan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, pelanggan $pelanggan)
    {
        try {
            $request->validate([
                "pelanggan_nama" => "required",
                "pelanggan_alamat" => "required",
                "pelanggan_notelp" => "required|max:13",
                "pelanggan_email" => "required|email",
            ]);

            $pelanggan->update($request->all());

            Cache::forget('pelanggan');
            Cache::forget('pelanggan_' . $pelanggan->pelanggan_id);

            return response()->json([
                "success" => true,
                "message" => "berhasil update pelanggan",
                "data" => $pelanggan
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
    public function destroy(pelanggan $pelanggan)
    {
        try {
            $pelanggan->delete();
            Cache::forget('pelanggan_' . $pelanggan->pelanggan_id);
            Cache::forget('pelanggan');

            return response()->json([
                "success" => true,
                "message" => "berhasil di hapus",
                "data" => $pelanggan
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
