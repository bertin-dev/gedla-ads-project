<?php

use App\Http\Controllers\Admin\ArchiveController;
use App\Http\Controllers\Admin\CalendarAdminController;
use App\Http\Controllers\Admin\FoldersAccessController;
use App\Http\Controllers\Admin\SignatureController;
use App\Http\Controllers\Admin\WorkflowManagementController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\createDocumentController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\NotificationsController;
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
    Route::post('storeFile', [OcrController::class, 'postUploadFile'])->name('post-upload-file');

    //OPERATIONS
    Route::post('operation/store', [OperationController::class, 'store'])->name('operation.store');
    Route::get('operation/index', [OperationController::class, 'index'])->name('operation.index');

    //PARAPHEUR
    Route::get('parapheur/index', [ParapheurController::class, 'index'])->name('parapheur.index');
    Route::get('parapheur/show', [ParapheurController::class, 'show'])->name('parapheur.show');
    Route::post('parapheur/download', [ParapheurController::class, 'download'])->name('parapheur.download');
    Route::get('parapheur/upload', [ParapheurController::class, 'upload'])->name('parapheur.upload');
    Route::post('parapheur/upload', [ParapheurController::class, 'postUpload'])->name('parapheur.postUpload');
    Route::post('parapheur/media', [ParapheurController::class, 'storeMedia'])->name('parapheur.storeMedia');

    //find user by id
    Route::get('find/{id}', [UsersController::class, 'findUser'])->name('users.user-find');

    //Calendar
    Route::get('calendar', [CalendarController::class, 'index'])->name('calendar.home');
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
    Route::post('workflow-management/validateDocument', [WorkflowManagementController::class, 'validateDocument'])->name('workflow-management.validateDocument');

    //Signature
    Route::resource('signature', SignatureController::class);
    Route::delete('signature/destroy', [SignatureController::class, 'massDestroy'])->name('signature.massDestroy');
    Route::post('import/media_signature', [SignatureController::class, 'storeMedia'])->name('signature.store-media');
    Route::post('signature/upload', [SignatureController::class, 'postUpload'])->name('signature.postUpload');

    //Import & Export users
    Route::get('display-view', [ExcelController::class, 'displayView'])->name('users.display-view-importation');
    Route::post('import-users', [ExcelController::class, 'importUsers'])->name('import-users');
    Route::get('export-users', [ExcelController::class, 'exportUsers'])->name('export-users');

    //Archive document
    Route::resource('archive', ArchiveController::class);

    //Calendar admin
    Route::get('calendar-admin', [CalendarAdminController::class, 'index'])->name('calendar.admin');
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

//Import Signature or Paraphe
Route::get('signature/upload', [SignatureController::class, 'upload'])->name('signature.upload');
//Create document
Route::get('create-document', [createDocumentController::class, 'create'])->name('create-document');
Route::post('upload-document', [createDocumentController::class, 'upload'])->name('upload-document');


//Route::post('uploadOCR', [PostUploadOCRController::class, 'postUploadOCR'])->name('post-upload-ocr');

