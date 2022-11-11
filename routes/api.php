<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\V1\Admin\PermissionsApiController;
use \App\Http\Controllers\Api\V1\Admin\RolesApiController;
use \App\Http\Controllers\Api\V1\Admin\UsersApiController;
use \App\Http\Controllers\Api\V1\Admin\ProjectsApiController;
use \App\Http\Controllers\Api\V1\Admin\FoldersApiController;



Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:api']], function () {
    // Permissions
    Route::apiResource('permissions', PermissionsApiController::class);

    // Roles
    Route::apiResource('roles', RolesApiController::class);

    // Users
    Route::apiResource('users', UsersApiController::class);

    // Projects
    Route::apiResource('projects', ProjectsApiController::class);

    // Folders
    Route::post('folders/media', [FoldersApiController::class, 'storeMedia'])->name('folders.storeMedia');
    Route::apiResource('folders', FoldersApiController::class);
});
