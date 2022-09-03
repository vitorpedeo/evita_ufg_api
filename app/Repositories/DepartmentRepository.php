<?php

namespace App\Repositories;

use App\Models\Department;

class DepartmentRepository
{
    private Department $model;

    public function __construct(Department $model)
    {
        $this->model = $model;
    }

    public function findAll()
    {
        return $this->model->all();
    }

    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    public function findByRegional(string $regional)
    {
        return $this->model->where('regional', $regional)->get();
    }
}
