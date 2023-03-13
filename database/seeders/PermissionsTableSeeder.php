<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(PermissionsTableSeeder::class);

        $permissions = [
            [
                'id'    => 1,
                'title' => 'user_management_access',
            ],
            [
                'id'    => 2,
                'title' => 'permission_create',
            ],
            [
                'id'    => 3,
                'title' => 'permission_edit',
            ],
            [
                'id'    => 4,
                'title' => 'permission_show',
            ],
            [
                'id'    => 5,
                'title' => 'permission_delete',
            ],
            [
                'id'    => 6,
                'title' => 'permission_access',
            ],
            [
                'id'    => 7,
                'title' => 'role_create',
            ],
            [
                'id'    => 8,
                'title' => 'role_edit',
            ],
            [
                'id'    => 9,
                'title' => 'role_show',
            ],
            [
                'id'    => 10,
                'title' => 'role_delete',
            ],
            [
                'id'    => 11,
                'title' => 'role_access',
            ],
            [
                'id'    => 12,
                'title' => 'user_create',
            ],
            [
                'id'    => 13,
                'title' => 'user_edit',
            ],
            [
                'id'    => 14,
                'title' => 'user_show',
            ],
            [
                'id'    => 15,
                'title' => 'user_delete',
            ],
            [
                'id'    => 16,
                'title' => 'user_access',
            ],
            [
                'id'    => 17,
                'title' => 'project_create',
            ],
            [
                'id'    => 18,
                'title' => 'project_edit',
            ],
            [
                'id'    => 19,
                'title' => 'project_show',
            ],
            [
                'id'    => 20,
                'title' => 'project_delete',
            ],
            [
                'id'    => 21,
                'title' => 'project_access',
            ],
            [
                'id'    => 22,
                'title' => 'folder_create',
            ],
            [
                'id'    => 23,
                'title' => 'folder_edit',
            ],
            [
                'id'    => 24,
                'title' => 'folder_show',
            ],
            [
                'id'    => 25,
                'title' => 'folder_delete',
            ],
            [
                'id'    => 26,
                'title' => 'folder_access',
            ],
            [
                'id'    => 27,
                'title' => 'profile_password_edit',
            ],
            [
                'id'    => 28,
                'title' => 'ocr_access',
            ],
            [
                'id'    => 29,
                'title' => 'page_access',
            ],
            [
                'id'    => 30,
                'title' => 'paraph_access',
            ],
            [
                'id'    => 31,
                'title' => 'safe_access',
            ],
            [
                'id'    => 32,
                'title' => 'lunch_access',
            ],
            [
                'id'    => 33,
                'title' => 'dispath_access',
            ],
            [
                'id'    => 34,
                'title' => 'design_access',
            ],
            [
                'id'    => 35,
                'title' => 'audit_log_show',
            ],
            [
                'id'    => 36,
                'title' => 'audit_log_access',
            ],
            [
                'id'    => 37,
                'title' => 'user_alert_create',
            ],
            [
                'id'    => 38,
                'title' => 'user_alert_show',
            ],
            [
                'id'    => 39,
                'title' => 'user_alert_delete',
            ],
            [
                'id'    => 40,
                'title' => 'user_alert_access',
            ],
            [
                'id'    => 41,
                'title' => 'operation_access',
            ],
            [
                'id'    => 42,
                'title' => 'folder_access_create',
            ],
            [
                'id'    => 43,
                'title' => 'folder_access_show',
            ],
            [
                'id'    => 44,
                'title' => 'folder_access_edit',
            ],
            [
                'id'    => 45,
                'title' => 'folder_access_delete',
            ],
            [
                'id'    => 46,
                'title' => 'workflow_validation_management_access',
            ],
            [
                'id'    => 47,
                'title' => 'signature_access',
            ],
            [
                'id'    => 48,
                'title' => 'download_access',
            ],
            [
                'id'    => 49,
                'title' => 'open_file_access',
            ],
            [
                'id'    => 50,
                'title' => 'archive_file_access',
            ],
            [
                'id'    => 51,
                'title' => 'signature_access_show',
            ],
            [
                'id'    => 52,
                'title' => 'signature_access_edit',
            ],
            [
                'id'    => 53,
                'title' => 'signature_access_delete',
            ],
            [
                'id'    => 54,
                'title' => 'signature_access_create',
            ],
            [
                'id'    => 55,
                'title' => 'validate_file_access',
            ],
            [
                'id'    => 56,
                'title' => 'workflow_management_access_create',
            ],
            [
                'id'    => 57,
                'title' => 'workflow_management_access_show',
            ],
            [
                'id'    => 58,
                'title' => 'workflow_management_access_edit',
            ],
            [
                'id'    => 59,
                'title' => 'workflow_management_access_delete',
            ],
            [
                'id'    => 60,
                'title' => 'import_user',
            ],
            [
                'id'    => 61,
                'title' => 'archive_show',
            ],
            [
                'id'    => 62,
                'title' => 'archive_delete',
            ],


        ];


        Permission::insert($permissions);
    }
}
