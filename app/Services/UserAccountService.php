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
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        try {
            $validData = $validator->validated();
            $validData['password'] = Hash::make($validData['password']);

            $userAccount = $this->repository->create($validData);

            return response()->json(['success' => true, 'data' => $userAccount], 201);
        } catch (\Exception $e) {
            Log::error('Erro ao tentar registrar um novo usuário', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function login($data)
    {
        $validator = Validator::make($data, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        try {
            $validData = $validator->validated();

            $userAccount = $this->repository->findByEmail($validData['email']);

            if (!$userAccount) {
                return response()->json(['success' => false, 'message' => 'Invalid email/password'], 401);
            }

            if (!Hash::check($validData['password'], $userAccount->password)) {
                return response()->json(['success' => false, 'message' => 'Invalid email/password'], 401);
            }

            return response()->json([
                'success' => true,
                'data' => $userAccount,
                'token' => $userAccount->createToken('auth_token')->plainTextToken,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao logar usuário', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        $currentUser = Auth::user();

        $currentUser->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Successfully logout'], 200);
    }

    public function invalidToken()
    {
        return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
    }
}
