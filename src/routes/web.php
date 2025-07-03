<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InteractionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Companyリソース（CRUD）
    Route::resource('companies', CompanyController::class);

    // Interactionの作成（Companyに紐づく）
    Route::post('/companies/{company}/interactions', [InteractionController::class, 'store'])->name('interactions.store');

    // Interactionの編集・更新・削除（ID単体で操作）
    Route::get('/interactions/{interaction}/edit', [InteractionController::class, 'edit'])->name('interactions.edit');
    Route::put('/interactions/{interaction}', [InteractionController::class, 'update'])->name('interactions.update');
    Route::delete('/interactions/{interaction}', [InteractionController::class, 'destroy'])->name('interactions.destroy');
});

// ローカル開発用の即ログインルート
if (app()->environment('local')) {
    Route::get('/dev-login', function () {
        $user = User::first(); // 最初のユーザーに即ログイン
        Auth::login($user);
        return redirect('/companies'); // 任意の遷移先
    });
}

require __DIR__.'/auth.php';
