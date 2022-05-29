<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(RegisterController::class)->group(function(){
    Route::post('login', 'login')->name('auth.login');
    // Route::post('register', 'register'); //uncomment this route to register the super admin and comment the constraintsin the controller

});

Route::middleware('auth:sanctum')->group( function () {
    Route::post('register', [RegisterController::class,'register'])->name('auth.register');
    Route::post('logout', [RegisterController::class,'logout'])->name('auth.logout');
    Route::resource('attendances', AttendanceController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::post('signout', [AttendanceController::class,'signout'])->name('auth.signout');
    Route::get('today/attendance', [AttendanceController::class,'todayAttendance']);
    Route::get('week/attendance', [AttendanceController::class,'weekAttendance']);
    Route::get('month/attendance', [AttendanceController::class,'MonthAttendance']);

    
});
Route::post('password-reset-link', [UserController::class,'sendPasswordResetLinkEmail'])->middleware('throttle:5,1')->name('password.email');
Route::post('/password/reset', [UserController::class,'resetPassword'])->name('password.reset');



