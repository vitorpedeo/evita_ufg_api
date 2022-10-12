<?php

namespace App\Services;

use App\Repositories\UserAccountRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserAccountService
{
    private UserAccountRepository $repository;

    public function __construct(UserAccountRepository $repository)
    {
        $this->repository = $repository;
    }

    public function register(array $data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:user_account',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('name')) {
                return response()->json(['success' => false, 'message' => 'Informe um nome válido!'], 400);
            }

            if ($validator->errors()->has('email')) {
                return response()->json(['success' => false, 'message' => 'Informe um email válido!'], 400);
            }

            if ($validator->errors()->has('password')) {
                return response()->json(['success' => false, 'message' => 'Informe uma senha válida!'], 400);
            }

            return response()->json(['success' => false, 'message' => 'Dados inválidos!'], 400);
        }

        try {
            $validData = $validator->validated();
            $validData['password'] = Hash::make($validData['password']);

            $userAccount = $this->repository->create($validData);

            return response()->json(['success' => true, 'data' => $userAccount], 201);
        } catch (\Exception $e) {
            Log::error('Failed to register new user', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json(['success' => false, 'message' => 'Não foi possível criar sua conta no momento.'], 500);
        }
    }

    public function login($data)
    {
        $validator = Validator::make($data, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('email')) {
                return response()->json(['success' => false, 'message' => 'Informe um email válido!'], 400);
            }

            if ($validator->errors()->has('password')) {
                return response()->json(['success' => false, 'message' => 'Informe uma senha válida!'], 400);
            }

            return response()->json(['success' => false, 'message' => 'Dados inválidos!'], 400);
        }

        try {
            $validData = $validator->validated();

            $userAccount = $this->repository->findByEmail($validData['email']);

            if (!$userAccount) {
                return response()->json(['success' => false, 'message' => 'Email/senha inválidos!'], 401);
            }

            if (!Hash::check($validData['password'], $userAccount->password)) {
                return response()->json(['success' => false, 'message' => 'Email/senha inválidos!'], 401);
            }

            return response()->json([
                'success' => true,
                'data' => $userAccount,
                'token' => $userAccount->createToken('auth_token')->plainTextToken,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to login', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json(['success' => false, 'message' => 'Não foi possível realizar o login no momento.'], 500);
        }
    }

    public function logout()
    {
        $currentUser = Auth::user();

        $currentUser->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Logout realizado com sucesso!'], 200);
    }

    public function invalidToken()
    {
        return response()->json(['success' => false, 'message' => 'Token inválido!'], 401);
    }
}
