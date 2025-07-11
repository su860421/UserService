<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorizationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizationsController;

Route::name('api.')->prefix('v1')->group(function () {
    // Health check
    Route::get('health-check', function () {
        return response()->json(['status' => 'ok']);
    })->name('health_check');

    // Email verification
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->name('verification.verify');

    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot_password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset_password');
    Route::post('/resend-verification', [AuthController::class, 'resendVerificationEmail'])->name('resend_verification');

    // Protected routes
    Route::middleware(['auth:api'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change_password');

        // User management routes (with email verification)
        Route::middleware(['user.email.verified'])->group(function () {
            Route::apiResource('users', UserController::class);
            Route::patch('users/{user}/organizations', [UserController::class, 'updateOrganizations'])->name('users.update_organizations');
            // Authorization routes
            Route::prefix('authorization')->name('authorization.')->group(function () {
                Route::put('users/{id}/roles', [AuthorizationController::class, 'assignRolesToUser'])->name('users.assign_roles');
                Route::get('permissions', [AuthorizationController::class, 'permissionIndex'])->name('permissions.index');
                Route::apiResource('roles', AuthorizationController::class);
                Route::put('roles/{id}/permissions', [AuthorizationController::class, 'assignPermissionToRole'])->name('roles.permissions');
            });

            // Organizations routes - 特定路由必須在參數路由之前
            Route::get('organizations/tree', [OrganizationsController::class, 'tree'])->name('organizations.tree');
            Route::get('organizations/{id}/children', [OrganizationsController::class, 'children'])->name('organizations.children');
            Route::get('organizations/{id}/users', [OrganizationsController::class, 'users'])->name('organizations.users');
            Route::get('organizations/{id}/stats', [OrganizationsController::class, 'stats'])->name('organizations.stats');
            Route::apiResource('organizations', OrganizationsController::class);
        });
    });
});
