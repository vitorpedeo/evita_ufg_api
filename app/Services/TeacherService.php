<?php

namespace App\Services;

use App\Repositories\TeacherRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
            return response()->json(['success' => false, 'message' => 'Teacher not found'], 404);
        }

        try {
            $teacher = $this->repository->findById($id);

            Log::info('Successfully found teacher', [
                'user' => Auth::user(),
                'teacher' => $teacher,
            ]);

            return response()->json(['success' => true, 'data' => $teacher], 200);
        } catch (\Exception $e) {
            Log::error('Failed to find teacher', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user' => Auth::user(),
                'teacher' => $teacher,
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getByDepartmentId(int $departmentId)
    {
        $validator = Validator::make(['departmentId' => $departmentId], [
            'departmentId' => 'required|integer|exists:teacher,department_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Invalid department'], 400);
        }

        try {
            $teachers = $this->repository->findByDepartmentId($departmentId);

            Log::info('Successfully found teachers from department', [
                'user' => Auth::user(),
                'department' => $departmentId,
            ]);

            return response()->json(['success' => true, 'data' => $teachers], 200);
        } catch (\Exception $e) {
            Log::error('Failed to find teachers from department', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user' => Auth::user(),
                'department' => $departmentId,
            ]);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
