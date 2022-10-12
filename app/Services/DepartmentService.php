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

            Log::info('Successfully found departments', [
                'user' => Auth::user(),
            ]);

            return response()->json(['success' => true, 'data' => $departments], 200);
        } catch (\Exception $e) {
            Log::error('Failed to find departments', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user' => Auth::user(),
            ]);

            return response()->json(['success' => false, 'message' => 'Não foi possível encontrar os departamentos no momento.'], 500);
        }
    }
}
