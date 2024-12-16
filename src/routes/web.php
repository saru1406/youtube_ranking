<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\YoutubeRank\YoutubeRankController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [YoutubeRankController::class, 'dailyTrend'])->name('daily.trend');
Route::get('/week', [YoutubeRankController::class, 'weekTrend'])->name('week.trend');
Route::get('/trend/{categoryName}', [YoutubeRankController::class, 'dailyTrendByCategory'])->name('daily.trend.category');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/redis-test', function () {
    // Redisにキーと値を保存
    Redis::set('test_key', 'Hello Redis!');

    // Redisから値を取得
    $value = Redis::get('test_key');

    // 結果を返す
    return 'Stored value in Redis: '.$value;
});

require __DIR__.'/auth.php';
