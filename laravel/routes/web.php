<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\QuizProgressController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\AdminAuthController;


Route::get('/', function ()
{
    return redirect('/admin/login');
});

/*
|--------------------------------------------------------------------------
| Jalur Autentikasi Admin (Bisa Diakses Tanpa Login)
|--------------------------------------------------------------------------
*/
Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');


/*
|--------------------------------------------------------------------------
| Jalur Khusus Admin (DIKUNCI TOTAL via Middleware Auth & Admin)
|--------------------------------------------------------------------------
| Hanya user yang SUDAH LOGIN dan memiliki ROLE 'admin' yang bisa masuk ke sini.
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function ()
{
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Manajemen Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('admin.courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('admin.courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('admin.courses.store');
    Route::get('/courses/{id}', [CourseController::class, 'show'])->name('admin.courses.show');
    Route::post('/courses/{id}/content', [CourseController::class, 'storeContent'])->name('admin.courses.storeContent');
    Route::get('/courses/{id}/edit', [CourseController::class, 'edit'])->name('admin.courses.edit');
    Route::put('/courses/{id}', [CourseController::class, 'update'])->name('admin.courses.update');
    Route::delete('/courses/{id}', [CourseController::class, 'destroy'])->name('admin.courses.destroy');

    // Manajemen Students
    Route::get('/students', [StudentController::class, 'index'])->name('admin.students.index');
    Route::get('/students/{id}', [StudentController::class, 'show'])->name('admin.students.show');
    Route::get('/api/students/{id}', [StudentController::class, 'apiShow'])->name('students.apiShow');
    Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('admin.students.destroy');
    Route::post('/students', [StudentController::class, 'store'])->name('admin.students.store');

    // Manajemen Pembelian / Transaksi
    Route::get('/pembelian', [TransactionController::class, 'index'])->name('admin.pembelian.index');

    // Manajemen Quiz & Progress
    Route::get('/quiz-progress', [QuizProgressController::class, 'index'])->name('admin.quiz.index');
    Route::get('/quiz-progress/{id}', [QuizProgressController::class, 'show'])->name('admin.quiz.show');
    Route::get('/quiz-progress/{course}/manage', [QuizProgressController::class, 'manage'])->name('admin.quiz.manage');
    Route::post('/quiz-progress/{course}/storeQuiz', [QuizProgressController::class, 'storeQuiz'])->name('admin.quiz.store');
    Route::get('/quiz/{quiz}/edit', [QuizProgressController::class, 'editQuiz'])->name('admin.quiz.edit');
    Route::put('/quiz/{quiz}', [QuizProgressController::class, 'updateQuiz'])->name('admin.quiz.update');
    Route::delete('/quiz/{quiz}', [QuizProgressController::class, 'destroyQuiz'])->name('admin.quiz.destroy');

    // Manajemen Sertifikat dan Pembayaran
    Route::get('/admin/certificates', [CertificateController::class, 'index'])->name('admin.certificates.index');
    Route::post('/admin/certificates/issue/{userId}/{courseId}', [CertificateController::class, 'issue'])->name('admin.certificates.issue');
    Route::get('/admin/certificates/preview/{id}', [CertificateController::class, 'preview'])->name('admin.certificates.preview');
    Route::get('/admin/pembelian/pdf', [TransactionController::class, 'exportPdf'])->name('admin.pembelian.pdf');
    Route::get('/admin/certificates/download/{id}', [CertificateController::class, 'download'])->name('admin.certificates.download');
});
