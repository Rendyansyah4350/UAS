<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\CertificateApiController;

// --- API Publik (Bisa diakses tanpa login) ---
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

// --- API Privat (Wajib bawa Token / auth:sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);    // Ambil data profil
    Route::post('/logout', [AuthController::class, 'logout']); // Hapus token
    Route::post('/enrollments', [EnrollmentController::class, 'store']);
    Route::post('/enrollments', [EnrollmentController::class, 'store']); // Membeli
    Route::get('/enrollments', [EnrollmentController::class, 'index']); // melihat
    Route::middleware('auth:sanctum')->get('/courses/{course_id}/contents', [ContentController::class, 'index']);
    Route::middleware('auth:sanctum')->post('/progress', [ProgressController::class, 'markAsCompleted']);
    Route::middleware('auth:sanctum')->get('/courses/{course_id}/progress', [ProgressController::class, 'getProgress']);
    Route::middleware('auth:sanctum')->get('/courses/{course_id}/quizzes', [QuizController::class, 'index']);
    Route::post('/courses/{course_id}/quizzes/submit', [QuizController::class, 'submit']);
    Route::middleware('auth:sanctum')->get('/courses/{course_id}/certificate', [EnrollmentController::class, 'getCertificate']);
    Route::post('/quizzes', [QuizController::class, 'store']);
    Route::get('/courses/{course_id}/students', [EnrollmentController::class, 'getEnrolledStudents']);
    Route::get('/courses/{course_id}/progress/{user_id}', [ProgressController::class, 'getStudentProgress']);
    Route::get('/instructor/dashboard', [CourseController::class, 'dashboard']);
    Route::middleware('auth:sanctum')->post('/contents', [ContentController::class, 'store']);
    Route::post('/contents', [ContentController::class, 'store']);
    Route::get('/my-certificates', [CertificateApiController::class, 'index']);
    Route::post('/progress/mark-completed', [ProgressController::class, 'markAsCompleted']);
    Route::post('/progress/submit-quiz', [ProgressController::class, 'submitQuiz']);
    Route::get('/progress/course/{course_id}', [ProgressController::class, 'getProgress']);
    Route::post('/courses/{id}/rate', [CourseController::class, 'rate']);
    Route::put('/user/update', [AuthController::class, 'updateProfile']);
});
