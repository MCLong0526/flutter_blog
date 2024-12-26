<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $attr = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name' => $attr['name'],
            'email' => $attr['email'],
            'password' => bcrypt($attr['password']),
        ]);

        return response()->json([
            'message' => 'User registered',
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken,
        ], 200);
    }

    //login user
    public function login(Request $request)
    {
        $attr = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($attr)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        return response()->json([
            'message' => 'User logged in',
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken,
        ], 200);
    }

    //logout user
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'User logged out',
        ], 200);
    }

    //get user
    public function user()
    {
        return response([
            'user' => auth()->user(),
        ], 200);
    }

    public function update(Request $request)
    {

        $attr = $request->validate([
            'name' => 'required|string',
        ]);

        $image = $this->saveImage($request->image, 'profile');

        auth()->user()->update([
            'name' => $attr['name'],
            'image' => $image,
        ]);

        return response([
            'message' => 'User updated',
            'user' => auth()->user(),
        ], 200);

    }
}
