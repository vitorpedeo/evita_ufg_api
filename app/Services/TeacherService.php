<?php

namespace App\Services;

use App\Repositories\TeacherRepository;
use Illuminate\Support\Facades\Validator;

class TeacherService
{
    private TeacherRepository $repository;

    public function __construct(TeacherRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getById(int $id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:teacher,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        try {
            $teacher = $this->repository->findById($id);

            return response()->json($teacher, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getByDepartmentId(int $departmentId)
    {
        $validator = Validator::make(['departmentId' => $departmentId], [
            'departmentId' => 'required|integer|exists:teacher,department_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid department'], 400);
        }

        try {
            $teachers = $this->repository->findByDepartmentId($departmentId);

            return response()->json($teachers, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
