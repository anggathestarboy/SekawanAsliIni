<?php

namespace App\Http\Controllers;

use App\Models\pelangganData;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PelangganDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $pelangganData = pelangganData::all();

            return response()->json([
                "success" => true,
                "message" => "berhasil ambil data pelanggan_data",
                "data" => $pelangganData
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
                "pelanggan_data_pelanggan_id" => "required|exists:pelanggan,pelanggan_id",
                "pelanggan_data_jenis" => "required|in:KTP,SIM",
                "pelanggan_data_file" => "required|image|mimes:jpg,jpeg,png|max:2048",
            ]);

            $data = $request->all();

            if ($request->hasFile("pelanggan_data_file")) {
                $path = $request->file("pelanggan_data_file")->store("pelanggan_data", "public");
                $data["pelanggan_data_file"] = $path;
            }

            $pelangganData = pelangganData::create($data);
            
            return response()->json([
                "success" => true,
                "message" => "berhasil buat pelanggan_data",
                "data" => $pelangganData
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
    public function show(int $pelanggan_data_id)
    {
        try {
            $pelangganData = pelangganData::getDetail($pelanggan_data_id);

            return response()->json([
                "success" => true,
                "message" => "berhasil ambil data pelanggan_data",
                "data" => $pelangganData
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
    public function edit(pelangganData $pelangganData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, pelangganData $pelangganData)
    {
        try {
            $request->validate([
                "pelanggan_data_pelanggan_id" => "required|exists:pelanggan,pelanggan_id",
                "pelanggan_data_jenis" => "required|in:KTP,SIM",
                "pelanggan_data_file" => "nullable|image|mimes:jpg,jpeg,png|max:2048",
            ]);

            $data = $request->all();

            if ($request->hasFile("pelanggan_data_file")) {
                if ($pelangganData->pelanggan_data_file && Storage::disk('public')->exists($pelangganData->pelanggan_data_file)) {
                    Storage::disk('public')->delete($pelangganData->pelanggan_data_file);
                }
                $path = $request->file("pelanggan_data_file")->store("pelanggan_data", "public");
                $data["pelanggan_data_file"] = $path;
            }

            $pelangganData->update($data);

            return response()->json([
                "success" => true,
                "message" => "berhasil update pelanggan_data",
                "data" => $pelangganData
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
    public function destroy(pelangganData $pelangganData)
    {
        try {
            if ($pelangganData->pelanggan_data_file && Storage::disk('public')->exists($pelangganData->pelanggan_data_file)) {
                Storage::disk('public')->delete($pelangganData->pelanggan_data_file);
            }

            $pelangganData->delete();

            return response()->json([
                "success" => true,
                "message" => "berhasil di hapus",
                "data" => $pelangganData
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
