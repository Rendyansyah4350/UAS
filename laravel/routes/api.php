<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CertificateApiController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Api\NotificationApiController;

// --- API Publik (Bisa diakses tanpa login) ---
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{id}', [CourseController::class, 'show']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword']);
Route::post('/xendit/callback', [EnrollmentController::class, 'handleCallback']);
Route::get('/courses/{course_id}/contents', [ContentController::class, 'index']);
Route::get('/certificates/{id}/download', [CertificateApiController::class, 'downloadMobile']);


// --- API Privat (Wajib bawa Token / auth:sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);    // Ambil data profil
    Route::post('/logout', [AuthController::class, 'logout']); // Hapus token
    Route::post('/enrollments', [EnrollmentController::class, 'store']);
    Route::get('/enrollments', [EnrollmentController::class, 'index']); // melihat
    Route::post('/progress', [ProgressController::class, 'markAsCompleted']);
    Route::get('/courses/{course_id}/progress', [ProgressController::class, 'getProgress']);
    Route::get('/courses/{course_id}/quizzes', [QuizController::class, 'index']);
    Route::post('/courses/{course_id}/quizzes/submit', [QuizController::class, 'submit']);
    Route::get('/courses/{course_id}/certificate', [EnrollmentController::class, 'getCertificate']);
    Route::post('/quizzes', [QuizController::class, 'store']);
    Route::get('/courses/{course_id}/students', [EnrollmentController::class, 'getEnrolledStudents']);
    Route::get('/courses/{course_id}/progress/{user_id}', [ProgressController::class, 'getStudentProgress']);
    Route::get('/instructor/dashboard', [CourseController::class, 'dashboard']);
    Route::post('/contents', [ContentController::class, 'store']);
    Route::get('/my-certificates', [CertificateApiController::class, 'index']);
    Route::post('/progress/mark-completed', [ProgressController::class, 'markAsCompleted']);
    Route::post('/progress/submit-quiz', [ProgressController::class, 'submitQuiz']);
    Route::get('/progress/course/{course_id}', [ProgressController::class, 'getProgress']);
    Route::post('/courses/{id}/rate', [CourseController::class, 'rate']);
    Route::put('/user/update', [AuthController::class, 'updateProfile']);
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);
    Route::post('/quiz/submit', [QuizController::class, 'store']);
    Route::get('/notifications', [NotificationController::class, 'getNotifUser']);
    Route::post('/contents/mark-complete', [ContentController::class, 'markComplete']);
    Route::get('/notifications', [NotificationApiController::class, 'getNotifUser']);
    Route::post('/notifications/read/{id}', [NotificationApiController::class, 'markAsRead']);
});
