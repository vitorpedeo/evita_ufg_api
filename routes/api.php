<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserAccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function() {
    Route::post('/register', [UserAccountController::class, 'register'])->name('auth.register');
    Route::post('/login', [UserAccountController::class, 'login'])->name('auth.login');
});

/* Rota para lidar com os erros causados pelos tokens inválidos */
Route::get('/login', [UserAccountController::class, 'invalidToken'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('department')->group(function () {
        Route::get('/', [DepartmentController::class, 'getAll'])->name('department.getAll');
    });

    Route::prefix('teacher')->group(function () {
        Route::get('/{id}', [TeacherController::class, 'getById'])->name('teacher.getById');
        Route::get('/department/{id}', [TeacherController::class, 'getByDepartmentId'])->name('teacher.getByDepartmentId');
    });

    Route::get('/logout', [UserAccountController::class, 'logout'])->name('auth.logout');
});
