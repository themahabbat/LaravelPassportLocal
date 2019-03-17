<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class MainController extends Controller
{
    public function login(Request $req)
    {

        $req->request->add([
            'grant_type' => 'password',
            'client_id' => 2,
            'client_secret' => env('PASSPORT_GRANT'),
            'username' => request('username'),
            'password' => request('password'),
            'scope' => '*',
        ]);

        $tokenRequest = Request::create('/oauth/token', 'POST');
        $response = Route::dispatch($tokenRequest);

        return dd($response);
    }
}
