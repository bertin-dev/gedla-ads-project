<?php

return [
    'userManagement' => [
        'title' => 'User Management'
    ],
    'permission' => [
        'title' => 'Permission',
        'title_singular' => 'permission',
        'fields'  => [
            'id' => 'ID',
            'title' => 'title',
            'title_helper' => 'title helper'
        ]
    ],
    'role' => [
        'title' => 'Role',
        'title_singular' => 'role',
        'fields'  => [
            'id' => 'ID',
            'title' => 'title',
            'permissions' => 'permissions',
            'title_helper' => 'title helper',
            'permissions_helper' => 'permissions helper',
        ]
    ],
    'user' => [
        'title' => 'User',
        'title_singular' => 'user',
        'fields'  => [
            'id' => 'ID',
            'name' => 'name',
            'email' => 'email',
            'email_helper' => 'email helper',
            'password' => 'password',
            'password_helper' => 'password helper',
            'roles_helper' => 'roles helper',
            'name_helper' => 'name helper',
            'email_verified_at' => 'email verified at',
            'roles' => 'role',
        ]
    ],

    'project' => [
        'title' => 'Project',
        'title_singular' => 'project',
        'fields'  => [
            'id' => 'ID',
            'name' => 'name',
            'users' => 'users',
            'name_helper' => 'name helper',
            'users_helper' => 'users helper',
        ]
    ],

    'folder' => [
        'title' => 'Folder',
        'title_singular' => 'folder',
        'fields'  => [
            'id' => 'ID',
            'name' => 'name',
            'project' => 'project',
            'files' => 'folder',
            'parent' => 'parent',
            'name_helper' => 'name helper',
            'project_helper' => 'project helper',
            'files_helper' => 'files helper',
            'parent_helper' => 'parent helper',
        ]
    ],
];
