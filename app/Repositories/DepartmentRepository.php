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
        return $this->model->orderBy('name', 'asc')->get();
    }
}
