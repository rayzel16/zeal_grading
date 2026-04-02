<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
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


Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

});

Route::get('/redirect', function () {
    return Auth::user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
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


Route::prefix('exam')->middleware('auth')->group(function () {

    Route::get('/join', fn() => view('exam.join'))->name('exam.join.form');
    Route::post('/join', [ExamController::class, 'join'])->name('exam.join');

    Route::get('/{attempt}/start', [ExamController::class, 'start'])->name('exam.start');
    Route::post('/{attempt}/submit', [ExamController::class, 'submit'])->name('exam.submit');
    Route::get('/{attempt}/result', [ExamController::class, 'result'])->name('exam.result');

});

require __DIR__.'/auth.php';
