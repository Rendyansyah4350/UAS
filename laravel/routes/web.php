<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CourseController;

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
});
