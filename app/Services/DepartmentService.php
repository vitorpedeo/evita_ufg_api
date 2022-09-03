<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;
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

            // TODO: adicionar o nome do usuário no registro dos logs
            Log::info('O usuário X buscou todos os departamentos');

            return response()->json($departments, 200);
        } catch (\Exception $e) {
            // TODO: adicionar o nome do usuário no registro dos logs
            Log::error('Erro ao buscar todos os departamentos', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
