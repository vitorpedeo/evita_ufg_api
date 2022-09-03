<?php

namespace App\Http\Controllers;

use App\Services\TeacherService;
use Illuminate\Routing\Controller;

class TeacherController extends Controller
{
    private TeacherService $service;

    public function __construct(TeacherService $service)
    {
        $this->service = $service;
    }

    public function getById(int $id)
    {
        return $this->service->getById($id);
    }

    public function getByDepartmentId(int $departmentId)
    {
        return $this->service->getByDepartmentId($departmentId);
    }
}
