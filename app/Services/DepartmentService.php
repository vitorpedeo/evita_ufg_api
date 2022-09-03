<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;

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

            return response()->json($departments, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
