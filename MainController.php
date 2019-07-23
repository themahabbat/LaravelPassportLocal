<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class AuthCR extends Controller
{
    public function login(Request $req)
    {

        $req->request->add([
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_GRANT_ID'),
            'client_secret' => env('PASSPORT_GRANT_TOKEN'),
            'username' => $req->email,
            'password' => $req->password,
            'scope' => '*',
        ]);

        $tokenRequest = Request::create('/oauth/token', 'POST');
        $response = Route::dispatch($tokenRequest);


        $code = $response->getStatusCode();

        if ($code === 200) {
            return response()->json(json_decode($response->getContent()), 200);
        } else if ($code === 400) $error = "Invalid request. Please enter a email or password!";
        else if ($code === 401) $error = "Your credentials are incorrect!";
        else $error = "Something went wrong";

        if ($error) return response()->json(['error' => $error], 400);
    }

    public function register(Request $req)
    {
        $req->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);

        return User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => bcrypt($req->password)
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'Logged out successfully!'], 200);
    }
}
