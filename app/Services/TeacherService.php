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
            return response()->json(['error' => 'Teacher not found'], 404);
        }

        try {
            $teacher = $this->repository->findById($id);

            Log::info('Busca bem sucedida por um professor', [
                'user' => Auth::user(),
                'teacher' => $teacher,
            ]);

            return response()->json($teacher, 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar o professor', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user' => Auth::user(),
                'teacher' => $teacher,
            ]);

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

            Log::info('Busca bem sucedida por professores de um departamento', [
                'user' => Auth::user(),
                'department' => $departmentId,
            ]);

            return response()->json($teachers, 200);
        } catch (\Exception $e) {
            // TODO: adicionar o nome do usuário no registro dos logs
            Log::error('Erro ao buscar professores de um departamento', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'user' => Auth::user(),
                'department' => $departmentId,
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
