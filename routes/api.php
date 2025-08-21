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
use App\Http\Controllers\Dashboard\AdminUserController;
use App\Http\Controllers\Dashboard\MobileController;
use App\Http\Controllers\Dashboard\SuperadminController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\LogActivityController;
use App\Http\Controllers\UnitLoanController;
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
    // Route::get('/user', [AuthController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::post('/student/import', [StudentController::class, 'import']);
    Route::apiResource('/student', StudentController::class);

    Route::get('/user/data', [UserController::class, 'getUsersData']);
    Route::apiResource('/user', UserController::class);

    Route::get('/item/paginate', [ItemController::class, 'itemPaginate']);
    Route::apiResource('/item', ItemController::class);

    Route::get('/subitem/paginate', [SubItemController::class, 'SubItemPaginate']);
    Route::apiResource('/subitem', SubItemController::class);

    Route::apiResource('/major', MajorController::class);

    Route::get('/consumable-loan/history', [ConsumableLoanController::class, 'getConsumableLoanHistory']);
    Route::apiResource('/consumable-loan', ConsumableLoanController::class);

    Route::get('/consumable-item/data', [ConsumableItemController::class, 'getData']);
    Route::apiResource('/consumable-item', ConsumableItemController::class);
    
    Route::apiResource('/unit-items', UnitItemController::class);

    Route::post('/teacher/import', [TeacherController::class, 'import']);
    Route::get('/teacher/data', [TeacherController::class, 'getTeachersData']);
    Route::delete('/teacher/reset', [TeacherController::class, 'resetData']);
    Route::apiResource('/teacher', TeacherController::class);


    Route::post('/unit-loan/check', [UnitLoanController::class, 'getLoan']);
    Route::get('/unit-loan/history', [UnitLoanController::class, 'getLoanHistory']);
    Route::apiResource('/unit-loan', UnitLoanController::class);

    Route::apiResource('/log-activity', LogActivityController::class)->only('index');

    Route::prefix('/export')->controller(ExportController::class)->group(function () {
        Route::post('unit-loan', 'exportUnitLoan');
        Route::post('consumable-loan', 'exportConsumableLoan');
        Route::post('students', 'exportStudents');
        Route::post('teachers', 'exportTeachers');
        Route::post('unit-items', 'exportUnitItems');
        Route::post('consumable-items', 'exportConsumableItems');
    });

    Route::prefix('/dashboard')->group(function () {
        Route::get('/loan-report', [AdminUserController::class, 'getLoanReport']);

        Route::prefix('/mobile')->controller(MobileController::class)->group(function () {
            Route::get('/card', 'getCardData');
            Route::get('/latest-activity', 'latestActivity');
        });

        Route::prefix('/admin-user')->controller(AdminUserController::class)->group(function () {
            Route::get('/latest-activity', 'latestActivity');
            Route::get('/item-count', 'itemCount');
            Route::get('/item-count-percentage', 'indexAverageBorrowing');
        });

        Route::prefix('/superadmin')->controller(SuperadminController::class)->group(function () {
            Route::get('/most-borrowed', 'indexBorrowing');
            Route::get('/most-borrowed-percentage', 'indexAverageBorrowing');
            Route::get('/major-loans', 'getMajorLoans');
            Route::get('/items-loans-history', 'getItemsLoansHistory');
            Route::get('/card', 'index');
        });
    });
});
