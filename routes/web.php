<?php

use App\Http\Controllers\LanguageController;
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
use \App\Http\Controllers\PostUploadOCRController;
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
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
// Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', [ChangePasswordController::class, 'edit'])->name('password.edit');
        Route::post('password', [ChangePasswordController::class, 'update'])->name('password.update');
    }
});


Route::post('uploadOCR', [PostUploadOCRController::class, 'postUploadOCR'])->name('post-upload-ocr');

