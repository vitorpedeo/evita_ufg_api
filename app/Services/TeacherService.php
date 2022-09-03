<?php

namespace App\Services;

use App\Repositories\TeacherRepository;
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
            // TODO: adicionar o nome do usuário no registro dos logs
            Log::error('O usuário X fez uma busca inválida por um professor', [
                'message' => $validator->errors()->first(),
            ]);

            return response()->json(['error' => 'Teacher not found'], 404);
        }

        try {
            $teacher = $this->repository->findById($id);

            // TODO: adicionar o nome do usuário e o nome do professor no registro dos logs
            Log::info('O usuário X buscou o professor Y');

            return response()->json($teacher, 200);
        } catch (\Exception $e) {
            // TODO: adicionar o nome do usuário no registro dos logs
            Log::error('Erro ao buscar o professor', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
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
            // TODO: adicionar o nome do usuário no registro dos logs
            Log::error('O usuário X fez uma busca inválida por professores', [
                'message' => $validator->errors()->first(),
            ]);

            return response()->json(['error' => 'Invalid department'], 400);
        }

        try {
            $teachers = $this->repository->findByDepartmentId($departmentId);

            // TODO: adicionar o nome do usuário e o nome do departamento no registro dos logs
            Log::info('O usuário X buscou os professores do departamento Y');

            return response()->json($teachers, 200);
        } catch (\Exception $e) {
            // TODO: adicionar o nome do usuário no registro dos logs
            Log::error('Erro ao buscar o professor', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
