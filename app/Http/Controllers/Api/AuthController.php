<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use Session;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation fail',
                'errors' => $validator->errors()
            ], 401);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'Email & password does not match our record',
            ], 401);
        }

        return response()->json([
            'status' => true,
            'message' => 'login success',
            'token' => Auth::user()->createToken("API TOKEN")->plainTextToken,
            'user' => Auth::user()
        ], 200);
    }

    public function show(Request $request)
    {
        return $request->user();
    }

    public function logout()
    {
        Session::flush();

        Auth::user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'logout success',
        ], 200);
    }
}
