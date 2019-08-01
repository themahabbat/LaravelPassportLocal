<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\User;

class ApiAuthCR extends Controller
{
    public function login(Request $req)
    {

        $req->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);


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


            // Auth::attempt($req->all());
            return response()->json($response->getBody(), 200);

            //
        } //
        else if ($code === 400) $error = "Invalid request. Please enter a email or password!";
        else if ($code === 401) $error = "Your credentials are incorrect!";
        else $error = "Something went wrong";

        // if ($error) return redirect()->back()->withErrors(['error' => $error]);
        if ($error) return response()->json(['error' => $error], 400);
    }

    public function register(Request $req)
    {
        $req->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => bcrypt($req->password)
        ]);

        if ($user) return response()->json(['message' => 'success'], 200);
        else return response()->json(['message' => 'success'], 400);

        // Auth::attempt([
        //     'email' => $req->email,
        //     'password' => $req->password
        // ]);

        return redirect('/');
    }

    public function logout()
    {
        // Auth::logout();

        auth()->user()->tokens->each(function ($token) {
            $token->delete();
        });

        return response()->json(['message' => 'success'], 200);
    }
}
