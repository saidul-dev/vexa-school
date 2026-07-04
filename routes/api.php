<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [ApiController::class, 'login']);

Route::group(['middleware', ['auth:sanctum']], function () {
    Route::post('/user_details', [ApiController::class, 'userDetails']);
    Route::post('/routine', [ApiController::class, 'routine']);
    Route::post('/attendance', [ApiController::class, 'attendanceReport']);
    Route::post('/subjects', [ApiController::class, 'subjects']);
    Route::post('/syllabus_list', [ApiController::class, 'syllabus_list']);
    Route::post('/teacher_list', [ApiController::class, 'teacher_list']);
    Route::post('/book_list', [ApiController::class, 'book_list']);
    Route::post('/book_issue_list', [ApiController::class, 'book_issue_list']);
    Route::post('/exam_list', [ApiController::class, 'exam_list']);
    Route::post('/marks', [ApiController::class, 'marks']);
    Route::post('/profile_update', [ApiController::class, 'profile_update']);
    Route::post('/fee_list', [ApiController::class, 'fee_list']);
    Route::post('/logout', [ApiController::class, 'logout']);
    Route::post('/account_delete', [ApiController::class, 'account_delete']);
    Route::post('/change_profile_photo', [ApiController::class, 'change_profile_photo']);
});
