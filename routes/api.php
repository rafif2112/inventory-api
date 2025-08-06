<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SubItemController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ConsumableItemController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UnitItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConsumableLoanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::apiResource('user', UserController::class);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [AuthController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('/student', StudentController::class);
    Route::apiResource('/item', ItemController::class);
    Route::apiResource('/subitem', SubItemController::class);
    Route::apiResource('/major', MajorController::class);
    Route::apiResource('/consumable-loan', ConsumableLoanController::class);
    Route::apiResource('/consumable-item', ConsumableItemController::class);
    // Route::prefix('teachers')->group(function () {
    //     Route::get('/', [TeacherController::class, 'index']);
    //     Route::get('/{id}', [TeacherController::class, 'show']);
    // });

    Route::apiResource('/teacher', TeacherController::class)->only('index', 'show');
    Route::apiResource('/unit-items', UnitItemController::class);
});

