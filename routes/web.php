<?php

use App\Http\Controllers\Admin\FoldersAccessController;
use App\Http\Controllers\Admin\WorkflowManagementController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\ParapheurController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ProjectController;
use \App\Http\Controllers\FolderController;
use \App\Http\Controllers\Admin\HomeController;
use \App\Http\Controllers\Admin\PermissionsController;
use \App\Http\Controllers\Admin\UsersController;
use \App\Http\Controllers\Admin\ProjectsController;
use \App\Http\Controllers\Admin\RolesController;
use \App\Http\Controllers\Admin\FoldersController;
use \App\Http\Controllers\Auth\ChangePasswordController;
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

Route::redirect('/', '/projects');

Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Route::group(['middleware' => ['auth', 'user']], function() {
    Route::resource('projects', ProjectController::class)->only(['index', 'show']);

    Route::get('folders/upload', [FolderController::class, 'upload'])->name('folders.upload');
    Route::post('folders/media', [FolderController::class, 'storeMedia'])->name('folders.storeMedia');
    Route::post('folders/upload', [FolderController::class, 'postUpload'])->name('folders.postUpload');
    Route::resource('folders', FolderController::class)->except(['index', 'destroy']);

    //OCR
    Route::get('openOCR', [OcrController::class, 'openOCR'])->name('openOCR');
    Route::post('storeOCR', [OcrController::class, 'storeImgOCR'])->name('storeImgOCR');
    Route::post('uploadOCR', [OcrController::class, 'postUploadOCR'])->name('postUploadOCR');

    //OPERATIONS
    Route::post('operation/store', [OperationController::class, 'store'])->name('operation.store');
    Route::get('operation/index', [OperationController::class, 'index'])->name('operation.index');

    //PARAPHEUR
    Route::get('parapheur/index', [ParapheurController::class, 'index'])->name('parapheur.index');
    Route::post('parapheur/download', [ParapheurController::class, 'download'])->name('parapheur.download');


});

Auth::routes(['register' => false]);
// Admin

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    // Permissions
    Route::delete('permissions/destroy', [PermissionsController::class, 'massDestroy'])->name('permissions.massDestroy');
    Route::resource('permissions', PermissionsController::class);

    // Roles
    Route::delete('roles/destroy', [RolesController::class, 'massDestroy'])->name('roles.massDestroy');
    Route::resource('roles', RolesController::class);

    // Users
    Route::delete('users/destroy', [UsersController::class, 'massDestroy'])->name('users.massDestroy');
    Route::resource('users', UsersController::class);

    // Projects
    Route::delete('projects/destroy', [ProjectsController::class, 'massDestroy'])->name('projects.massDestroy');
    Route::resource('projects', ProjectsController::class);

    // Folders
    Route::delete('folders/destroy', [FoldersController::class, 'massDestroy'])->name('folders.massDestroy');
    Route::post('folders/media', [FoldersController::class, 'storeMedia'])->name('folders.storeMedia');
    Route::post('folders/ckmedia', [FoldersController::class, 'storeCKEditorImages'])->name('folders.storeCKEditorImages');
    Route::resource('folders', FoldersController::class);
    Route::resource('folders_access', FoldersAccessController::class);
    Route::delete('folders_access/destroy', [FoldersAccessController::class, 'massDestroy'])->name('folders_access.massDestroy');


    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // User Alerts
    Route::delete('user-alerts/destroy', 'UserAlertsController@massDestroy')->name('user-alerts.massDestroy');
    Route::get('user-alerts/read', 'UserAlertsController@read');
    Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

    // Global Search
    Route::get('global-search', 'GlobalSearchController@search')->name('globalSearch');

    // Workflow Managemement
    Route::resource('workflow-management', WorkflowManagementController::class);
    Route::post('workflow-management/media', [WorkflowManagementController::class, 'storeMedia'])->name('workflow-management.storeMedia');
    Route::post('workflow-management/hasReadMedia', [WorkflowManagementController::class, 'hasReadMedia'])->name('workflow-management.hasReadMedia');


});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
// Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', [ChangePasswordController::class, 'edit'])->name('password.edit');
        Route::post('password', [ChangePasswordController::class, 'update'])->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});


//Route::post('uploadOCR', [PostUploadOCRController::class, 'postUploadOCR'])->name('post-upload-ocr');

