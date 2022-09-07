<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DepartmentService
{
    private DepartmentRepository $repository;

    public function __construct(DepartmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        try {
            $departments = $this->repository->findAll();

            Log::info('Busca bem sucedida pelos departamentos', [
                'user' => Auth::user(),
            ]);

            return response()->json($departments, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar todos os departamentos', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user' => Auth::user(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
