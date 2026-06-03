<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register(Request $request, Exception $exception)
    {

        try {
            $request->validate([
                "admin_username" => "required|max:50",
                "admin_password" => "required|max:255",
            ]);

            $admin = Admin::create($request->all());


            $response = [
                "success" => true,
                "message" =>  "Successfully create user data",
                "data" => $admin
            ];


            return response()->json($response, 200);
        } catch (Exception $exception) {
            return response()->json([
                "success" => false,
                "message" => "There error in Internal Server",
                "data" => null,
                "errors" => $exception->getMessage()
            ], 500);
        }
    }



    public function login(Request $request)
    {
        try {
            $validasi =     $request->validate([
                "admin_username" => "required|max:50",
                "admin_password" => "required|max:255",



            ]);


            if (!$token = Auth::attempt([
                'admin_username' => $request->admin_username,
                'password' => $request->admin_password,
            ])) {
                return response()->json([
                    "success" => false,
                    "message" => "wrong username or password",
                ], 401);
            } else {
                return response()->json([
                    'success' => true,
                    "user" => Auth::user(),
                    'token' => $token

                ]);
            }
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
