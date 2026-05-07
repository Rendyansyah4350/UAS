<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TransactionController;


Route::get('/', function () {
    return view('welcome');
});



// Grouping route admin agar lebih rapi
Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/courses', [CourseController::class, 'index'])->name('admin.courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('admin.courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('admin.courses.store');
    Route::get('/courses/{id}', [CourseController::class, 'show'])->name('admin.courses.show');
    Route::post('/courses/{id}/content', [CourseController::class, 'storeContent'])->name('admin.courses.storeContent');
    Route::get('/courses/{id}/edit', [CourseController::class, 'edit'])->name('admin.courses.edit');
    Route::put('/courses/{id}', [CourseController::class, 'update'])->name('admin.courses.update');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->name('admin.courses.destroy');
    Route::get('/admin/students', [StudentController::class, 'index'])->name('admin.students.index');
    Route::get('/admin/students/{id}', [StudentController::class, 'show'])->name('admin.students.show');
    Route::get('/api/students/{id}', [App\Http\Controllers\Admin\StudentController::class, 'apiShow'])->name('students.apiShow');
    Route::get('/pembelian', [TransactionController::class, 'index'])->name('admin.pembelian.index');
});
