<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminTaskController;
use App\Http\Controllers\UserTaskController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminWithdrawalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\EarningController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebSettingsController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::get('/user/name/{sponsor_id}', [UserController::class, 'getUserNameBySponsorId']);
Route::post('/pre-login', [AuthController::class, 'preLogin']);
Route::post('/pre-register', [AuthController::class, 'preRegister']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/check-mobile', [AuthController::class, 'checkMobile']);


Route::middleware('auth:api')->group(function () {
    Route::get('/user', [UserController::class, 'userDetails']);
    Route::get('/referrals/direct', [UserController::class, 'directReferrals']);
    Route::get('/referrals/all-levels', [UserController::class, 'allLevelReferrals']);
    Route::get('/tasks', [UserTaskController::class, 'fetchTasks']);
    Route::post('/tasks/{task}/complete', [UserTaskController::class, 'completeTask']);
    Route::get('/earnings', [UserController::class, 'userEarnings']);
    Route::post('/withdrawals', [UserController::class, 'createWithdrawalRequest']);
    Route::get('/withdrawals', [UserController::class, 'getWithdrawals']);
    Route::get('/stats', [UserController::class, 'getStats']);
    Route::get('/earnings', [EarningController::class, 'index']);
    Route::get('/earnings/{earning}', [EarningController::class, 'show']);
    Route::get('/bank-account', [BankAccountController::class, 'getBankAccount']);
    Route::post('/bank-account', [BankAccountController::class, 'updateBankAccount']);
    Route::post('/update-password', [UserController::class, 'updatePassword']);
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::get('web-settings', [WebSettingsController::class, 'getSettings']);
    Route::post('/telegram-share', [UserController::class, 'telegramShare']);


});

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/users', [AdminAuthController::class, 'listUsers']);
    Route::post('/admin/tasks', [AdminTaskController::class, 'create']);
    Route::delete('admin/tasks/{id}', [AdminTaskController::class, 'deleteTask']);
    Route::put('/admin/tasks/{task}', [AdminTaskController::class, 'update']);
    Route::get('/admin/tasks', [AdminTaskController::class, 'list']);
    Route::get('/admin/withdrawals', [AdminWithdrawalController::class, 'listWithdrawalRequests']);
    Route::put('/admin/withdrawals/{withdrawal}', [AdminWithdrawalController::class, 'updateWithdrawalRequestStatus']);
    Route::post('web-settings', [WebSettingsController::class, 'saveSettings']);
});

