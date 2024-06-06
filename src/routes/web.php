<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware('auth')->group(function () {
    Route::get('/', [AttendanceController::class, 'index']);
    Route::get('/userlist', [AttendanceController::class, 'userlist']);
    Route::get('/index', [AttendanceController::class, 'index']);
    Route::post('/TimeIn', [AttendanceController::class, 'TimeIn']);
    Route::post('/TimeOut', [AttendanceController::class, 'TimeOut']);
    Route::post('/BreakIn', [AttendanceController::class, 'BreakIn']);
    Route::post('/BreakOut', [AttendanceController::class, 'BreakOut']);
    Route::post('/BreakTime', [AttendanceController::class, 'BreakTime']);
    Route::post('/DutyTime', [AttendanceController::class, 'DutyTime']);
    Route::get('/attendance', [AttendanceController::class, 'attendance']);
    Route::get('/attendance/data/{date}', [AttendanceController::class, 'getDataByDate']);
});
