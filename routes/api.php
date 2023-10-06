<?php
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\LinemanController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AuthController;

Route::controller(AuthController::class)->group(function () {
    Route::post('/user-register', 'userRegister')->name('register');
    Route::post('/user-login', 'userLogin')->name('login');
});

Route::controller(ComplaintController::class)->group(function () {
    Route::get('/district', 'getDistrict')->name('district');
    Route::get('/zone', 'getZone')->name('zone');
    Route::get('/area', 'getArea')->name('area');
    Route::post('/usercomplaints', 'storeComplaints');
    Route::get('/admincomplaints', 'getComplaints');
    Route::get('/user-registered-complaints/{user_id}', 'userRegisteredComplaints');
});

Route::controller(LinemanController::class)->group(function () {
    Route::post('/lineman-register', 'linemanRegister');
    Route::post('/lineman-login', 'linemanLogin');
    Route::get('/lineman/{lineman_id}', 'viewLineman');
    Route::post('/assign-task', 'assignTasktoLineman');
    Route::get('/view-lineman-tasks/{lineman_id}', 'viewLinemanTasks');
    Route::get('/get-status', 'getStatus');
    Route::patch('/update-status', 'updateStatus');
    Route::put('/solved-date','updateSolvedDate');

});