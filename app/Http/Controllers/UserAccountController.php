<?php

namespace App\Http\Controllers;

use App\Services\UserAccountService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserAccountController extends Controller
{
    private UserAccountService $service;

    public function __construct(UserAccountService $service)
    {
        $this->service = $service;
    }

    public function register(Request $request)
    {
        return $this->service->register([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'avatar_url' => $request->input('avatar_url'),
            'password' => $request->input('password'),
        ]);
    }

    public function login(Request $request)
    {
        return $this->service->login([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);
    }

    public function googleLogin(Request $request)
    {
        return $this->service->googleLogin([
            'id' => $request->input('id'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'avatar_url' => $request->input('avatar_url'),
        ]);
    }

    public function logout()
    {
        return $this->service->logout();
    }

    public function invalidToken()
    {
        return $this->service->invalidToken();
    }
}
