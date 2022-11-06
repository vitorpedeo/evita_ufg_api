<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserAccountController;
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
    Route::post('/google-login', [UserAccountController::class, 'googleLogin'])->name('auth.googleLogin');
});

/* Rota para lidar com os erros causados pelos tokens inválidos */
Route::get('/login', [UserAccountController::class, 'invalidToken'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('department')->group(function () {
        Route::get('/', [DepartmentController::class, 'getAll'])->name('department.getAll');
        Route::get('/{id}/teacher', [TeacherController::class, 'getByDepartmentId'])->name('teacher.getByDepartmentId');
    });

    Route::prefix('teacher')->group(function () {
        Route::get('/{id}', [TeacherController::class, 'getById'])->name('teacher.getById');
    });

    Route::prefix('comment')->group(function () {
        Route::post('/', [CommentController::class, 'save'])->name('comment.save');
        Route::patch('/{id}', [CommentController::class, 'update'])->name('comment.update');
        Route::delete('/{id}', [CommentController::class, 'delete'])->name('comment.delete');
    });

    Route::get('/logout', [UserAccountController::class, 'logout'])->name('auth.logout');
});
