<?php

namespace App\Http\Controllers;

use App\Services\DepartmentService;
use Illuminate\Routing\Controller;

class DepartmentController extends Controller
{
    private DepartmentService $service;

    public function __construct(DepartmentService $service)
    {
        $this->service = $service;
    }

    public function getAll()
    {
        return $this->service->getAll();
    }
}
