<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyAiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// トップページ：未ログインは /login、ログイン済みは /dashboard へ
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

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

    // ★ AI提案（Company 詳細画面から叩く）
    Route::post('/companies/{company}/ai/advise', [CompanyAiController::class, 'advise'])
        ->name('companies.ai.advise');
});

// ローカル開発用の即ログインルート
if (app()->environment('local')) {
    Route::get('/dev-login', function () {
        $user = User::first(); // 最初のユーザーに即ログイン
        Auth::login($user);
        return redirect('/companies'); // 任意の遷移先
    });
}

require __DIR__ . '/auth.php';
