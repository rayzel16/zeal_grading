<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminExamController;
use App\Http\Controllers\Admin\AdminQuestionController;
use App\Http\Controllers\Admin\AdminAnswerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\Student\ExamAttemptController;
use Illuminate\Support\Facades\Auth;

Route::get('/routes-view', function () {
    $routes = collect(Route::getRoutes())->map(function ($route) {
        return [
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
            'middleware' => implode(',', $route->middleware()),
        ];
    });

    return view('routes.index', compact('routes'));
});


Route::prefix('exam')->middleware('auth')->group(function () {

    // join exam (by code)
    Route::get('/join', fn() => view('exam.join'))->name('exam.join.form');
    Route::post('/join', [ExamController::class, 'join'])->name('exam.join');

    // start exam (uses EXAM, not attempt)
    Route::post('/{exam}/start', [ExamAttemptController::class, 'start'])
        ->name('exam.start');

    // take exam (view questions)
    Route::get('/attempt/{attempt}', [ExamAttemptController::class, 'take'])
        ->name('exam.take');

    // submit answers
    Route::post('/attempt/{attempt}/submit', [ExamAttemptController::class, 'submit'])
        ->name('exam.submit');

    // result
    Route::get('/attempt/{attempt}/result', [ExamAttemptController::class, 'result'])
        ->name('exam.result');
});


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('exams', AdminExamController::class);

    Route::prefix('exams/{exam}')
    ->name('exams.')
    ->group(function () {
        Route::resource('questions', AdminQuestionController::class);
    });

    Route::prefix('questions/{question}')
    ->name('questions.')
    ->group(function () {
        Route::resource('answers', AdminAnswerController::class);
    });

});

Route::get('/redirect', function () {
    return Auth::user()->role === 'admin'
        ? redirect()->route('admin.admin.dashboard')
        : redirect('/dashboard');
})->middleware('auth')->name('redirect');


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/student/attempts', [ExamAttemptController::class, 'history'])
    ->name('student.attempts')
    ->middleware('auth');

Route::post('/attempt/{attempt}/violation',
    [ExamAttemptController::class, 'logViolation']
)->name('attempt.violation');

Route::get('/attempt/{attempt}/StudentDisplayViolation',
    [ExamAttemptController::class, 'StudentDisplayViolation']
)->name('attempt.display.StudentDisplayViolation');

require __DIR__.'/auth.php';
