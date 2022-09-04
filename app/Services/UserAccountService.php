<?php

namespace App\Services;

use App\Repositories\UserAccountRepository;
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
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $validData = $validator->validated();
            $validData['password'] = Hash::make($validData['password']);

            $userAccount = $this->repository->create($validData);

            return response()->json($userAccount, 201);
        } catch (\Exception $e) {
            Log::error('Erro ao registrar usuário', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function login($data)
    {
        $validator = Validator::make($data, [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            $validData = $validator->validated();

            $userAccount = $this->repository->findByEmail($validData['email']);

            if (!$userAccount) {
                return response()->json(['error' => 'Invalid email/password'], 401);
            }

            if (!Hash::check($validData['password'], $userAccount->password)) {
                return response()->json(['error' => 'Invalid email/password'], 401);
            }

            return response()->json($userAccount, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao logar usuário', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
