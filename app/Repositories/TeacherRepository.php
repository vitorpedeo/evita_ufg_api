<?php

namespace App\Repositories;

use App\Models\Teacher;

class TeacherRepository
{
    private Teacher $model;

    public function __construct(Teacher $model)
    {
        $this->model = $model;
    }

    public function findById(int $id)
    {
        return $this->model->with('department')->find($id);
    }

    public function findByDepartmentId(int $departmentId)
    {
        return $this->model->where('department_id', $departmentId)->get();
    }
}
