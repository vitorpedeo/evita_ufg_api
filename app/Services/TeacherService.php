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
            return response()->json(['success' => false, 'message' => 'Professor não encontrado.'], 404);
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

            return response()->json(['success' => false, 'message' => 'Não foi possível encontrar o professor no momento.'], 500);
        }
    }

    public function getByDepartmentId(int $departmentId)
    {
        $validator = Validator::make(['departmentId' => $departmentId], [
            'departmentId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Departamento inválido.'], 400);
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

            return response()->json(['success' => false, 'message' => 'Não foi possível encontrar os professores no momento.'], 500);
        }
    }
}
