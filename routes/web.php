<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home redirection based on auth state
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('student.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Student Routes
    Route::middleware('role:student')->group(function () {
        Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
        Route::post('/student/upload', [StudentController::class, 'uploadCv'])->name('student.upload');
        Route::get('/student/print', [StudentController::class, 'printReport'])->name('student.print');
    });

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::post('/admin/internships', [AdminController::class, 'storeInternship'])->name('admin.internships.store');
        Route::put('/admin/internships/{id}', [AdminController::class, 'updateInternship'])->name('admin.internships.update');
        Route::post('/admin/internships/{id}/delete', [AdminController::class, 'destroyInternship'])->name('admin.internships.destroy');
        Route::get('/admin/students/{id}/analysis', [AdminController::class, 'viewStudentAnalysis'])->name('admin.students.analysis');
    });
});
