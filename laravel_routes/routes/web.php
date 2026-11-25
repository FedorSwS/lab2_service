<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;

/*
|--------------------------------------------------------------------------
| Маршруты новостного портала (Лабораторная работа №2, Задание 2, Вариант 4)
|--------------------------------------------------------------------------
*/

// Главная страница — лента новостей со счётчиками Like
Route::get('/', [NewsController::class, 'index'])->name('news.index');

// Просмотр отдельной новости по числовому идентификатору
Route::get('/news/{id}', [NewsController::class, 'show'])
    ->whereNumber('id')
    ->name('news.show');

// AJAX-маршрут для увеличения/уменьшения счётчика Like
Route::post('/news/{id}/like', [NewsController::class, 'toggleLike'])
    ->whereNumber('id')
    ->name('news.like');

// Статическая страница «О портале»
Route::get('/about', [NewsController::class, 'about'])->name('pages.about');

// Группа маршрутов администрирования новостей (CRUD)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/news', [NewsController::class, 'adminIndex'])->name('news.index');
    Route::post('/news', [NewsController::class, 'store'])->name('news.store');
    Route::put('/news/{id}', [NewsController::class, 'update'])->whereNumber('id')->name('news.update');
    Route::delete('/news/{id}', [NewsController::class, 'destroy'])->whereNumber('id')->name('news.destroy');
});
