<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        try {
            $kategori = Cache::remember('kategori', 60 * 60 * 24, function () {
                return Kategori::all()->toArray();
            });


            if ($kategori === null) {
                return response()->json([
                    "success" => true,
                    "message" => "berhasil ambil data kategori",
                    "data" => null
                ]);
            }

            return response()->json([
                "success" => true,
                "message" => "berhasil ambil data kategori",
                "data" => $kategori
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
                "kategori_nama" => "required",

            ]);


            $kategori = Kategori::create($request->all());
            
            Cache::forget('kategori');
            
            return response()->json([
                "success" => true,
                "message" => "berhasil buat kategori",
                "data" => $kategori
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
    public function show(int $kategori_id)
    {


        try {
        



            $kategori = Cache::remember('kategori_' . $kategori_id, 60 * 60 * 24, function () use ($kategori_id) {
                $detail = Kategori::getDetail($kategori_id);
                return $detail ? $detail->toArray() : null;
            });

          


        
            return response()->json([
                "success" => true,
                "message" => "berhasil ambil data kategori",
                "data" => $kategori
            ]);
        }
        catch (Exception $exception) {
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
    public function edit(Kategori $kategori)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kategori $kategori)
    {
        try {
            $request->validate([
                "kategori_nama" => "required",
            ]);

            $kategori->update($request->all());

            Cache::forget('kategori');
            Cache::forget('kategori_' . $kategori->kategori_id);

            return response()->json([
                "success" => true,
                "message" => "berhasil update kategori",
                "data" => $kategori
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
    public function destroy(Kategori $kategori)
    {


        try {
            $kategori->delete();
            Cache::forget('kategori_'. $kategori->kategori_id);
            Cache::forget('kategori');

            return response()->json([
                "success" => true,
                "message" => "berhasil di hapus",
                "data" => $kategori

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
