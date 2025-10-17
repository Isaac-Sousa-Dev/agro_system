<?php

use App\Exports\PropertiesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ProductionUnitController;
use App\Http\Controllers\HerdController;
use Maatwebsite\Excel\Facades\Excel;

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

Route::get('/', function () {
    return response()->json([
        'message' => 'Agricultural Management System API',
        'version' => '1.0.0',
        'endpoints' => [
            'auth' => [
                'POST /api/auth/login' => 'User login',
                'POST /api/auth/logout' => 'User logout',
                'GET /api/auth/user' => 'Get authenticated user',
                'GET /api/auth/me' => 'Get authenticated user (alias)',
                'POST /api/auth/register' => 'User registration'
            ],
            'farmers' => [
                'GET /api/farmers' => 'List farmers',
                'POST /api/farmers' => 'Create farmer',
                'GET /api/farmers/{id}' => 'Show farmer',
                'PUT /api/farmers/{id}' => 'Update farmer',
                'DELETE /api/farmers/{id}' => 'Delete farmer'
            ],
            'properties' => [
                'GET /api/properties' => 'List properties',
                'POST /api/properties' => 'Create property',
                'GET /api/properties/{id}' => 'Show property',
                'PUT /api/properties/{id}' => 'Update property',
                'DELETE /api/properties/{id}' => 'Delete property',
                'GET /api/properties/export/excel' => 'Export properties to Excel',
                'GET /api/properties/export/preview' => 'Preview export data'
            ],
            'production-units' => [
                'GET /api/production-units' => 'List production units',
                'POST /api/production-units' => 'Create production unit',
                'GET /api/production-units/{id}' => 'Show production unit',
                'PUT /api/production-units/{id}' => 'Update production unit',
                'DELETE /api/production-units/{id}' => 'Delete production unit'
            ],
            'herds' => [
                'GET /api/herds' => 'List herds',
                'POST /api/herds' => 'Create herd',
                'GET /api/herds/{id}' => 'Show herd',
                'PUT /api/herds/{id}' => 'Update herd',
                'DELETE /api/herds/{id}' => 'Delete herd',
                'GET /api/herds/export/pdf' => 'Export herds to PDF by farmer',
                'GET /api/herds/export/pdf/preview' => 'Preview herds PDF export'
            ]
        ]
    ]);
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/me', [AuthController::class, 'user']);
    });
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Farmers
    Route::apiResource('farmers', FarmerController::class);

    // Properties
    Route::get('/properties/export/excel', [PropertyController::class, 'export']);
    Route::get('/properties/export/preview', [PropertyController::class, 'exportPreview']);
    Route::apiResource('properties', PropertyController::class);



    // Production Units
    Route::apiResource('production-units', ProductionUnitController::class);

    // Herds
    Route::get('/herds/export/pdf', [HerdController::class, 'exportPdf']);
    Route::get('/herds/export/pdf/preview', [HerdController::class, 'exportPdfPreview']);
    Route::apiResource('herds', HerdController::class);
});


