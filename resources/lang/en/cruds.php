<?php

return [
    'userManagement' => [
        'title' => 'User Management',
        'title_singular' => 'User management',
    ],
    'permission' => [
        'title' => 'Permissions',
        'title_singular' => 'Permission',
        'fields'  => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'title'             => 'Title',
            'title_helper'      => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'created_by'        => 'Created By',
            'updated_by'        => 'Updated By',
        ]
    ],
    'role' => [
        'title' => 'Roles',
        'title_singular' => 'Role',
        'fields'  => [
            'id' => 'ID',
            'title' => 'title',
            'permissions' => 'permissions',
            'title_helper' => 'title helper',
            'permissions_helper' => 'permissions helper',
            'created_by' => 'Created by',
            'updated_by' => 'Updated by',
        ]
    ],
    'user' => [
        'title' => 'User',
        'title_singular' => 'User',
        'fields'  => [
            'id'                       => 'ID',
            'id_helper'                => ' ',
            'name'                     => 'Name',
            'name_helper'              => ' ',
            'email'                    => 'Email',
            'email_helper'             => ' ',
            'email_verified_at'        => 'Email verified at',
            'email_verified_at_helper' => ' ',
            'password'                 => 'Password',
            'password_helper'          => ' ',
            'roles'                    => 'Roles',
            'roles_helper'             => ' ',
            'remember_token'           => 'Remember Token',
            'remember_token_helper'    => ' ',
            'created_at'               => 'Created at',
            'created_at_helper'        => ' ',
            'updated_at'               => 'Updated at',
            'updated_at_helper'        => ' ',
            'deleted_at'               => 'Deleted at',
            'deleted_at_helper'        => ' ',
            'created_by'               => 'User created',
        ]
    ],

    'project' => [
        'title'          => 'Entity',
        'title_singular' => 'Entity',
        'workflow_management' => 'Workflow Management',
        'fields'  => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Name',
            'name_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'thumbnail'         => 'Thumbnail',
            'thumbnail_helper'  => ' ',
            'users'             => 'User(s)',
            'users_helper'      => ' ',
            'created_by'        => 'Created by',
            'updated_by'        => 'Updated by',
        ]
    ],

    'folder' => [
        'title'          => 'Folder',
        'title_singular' => 'Folder',
        'fields'  => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Name',
            'desc'              => 'Description',
            'desc_helper'       => ' ',
            'name_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'parent'            => 'Parent',
            'parent_helper'     => ' ',
            'project'           => 'Entity',
            'project_helper'    => ' ',
            'files'             => 'File(s)',
            'files_helper'      => ' ',
            'created_by'        => 'Created by',
            'updated_by'        => 'Updated by',
        ]
    ],

    'auditLog' => [
        'title'          => 'Audit Logs',
        'title_singular' => 'Audit Log',
        'fields'         => [
            'id'                  => 'ID',
            'id_helper'           => ' ',
            'description'         => 'Description',
            'description_helper'  => ' ',
            'subject_id'          => 'Subject ID',
            'subject_id_helper'   => ' ',
            'subject_type'        => 'Subject Type',
            'subject_type_helper' => ' ',
            'user_id'             => 'User ID',
            'user_id_helper'      => ' ',
            'properties'          => 'Properties',
            'properties_helper'   => ' ',
            'host'                => 'Host',
            'host_helper'         => ' ',
            'created_at'          => 'Created at',
            'created_at_helper'   => ' ',
            'updated_at'          => 'Updated at',
            'updated_at_helper'   => ' ',
        ],
    ],

    'userAlert' => [
        'title'          => 'User Alerts',
        'title_singular' => 'User Alert',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'alert_text'        => 'Alert Text',
            'alert_text_helper' => ' ',
            'alert_link'        => 'Alert Link',
            'alert_link_helper' => ' ',
            'user'              => 'Users',
            'user_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'created_by'        => 'Created by',
        ],
    ],

    'userParaph' => [
        'title' => 'Paraph Management',
        'title_singular' => 'Paraph management',
    ],

    'folder_access' => [
        'folder_management'  => 'Folder Management',
        'access_folder'      => 'Access Folder',
        'title_singular' => 'Access Folder',
        'fields'  => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'user_access'       => 'User Access',
            'user_access_helper'       => ' ',
            'folder_access'         => 'Folder Access',
            'folder_access_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
        ]
    ],

    'workflow_management' => [
        'title'          => 'Workflow Management',
        'new_workflow'      => 'Workflow validation',
        'title_singular' => 'Workflow Management',
        'term'                  =>  'Term',
        'priority'              =>  'Priority',
        'visibility'            =>  'Visibility',
        'sender'                =>  'Sender',
        'receiver'              =>  'Receiver',
        'operation_state'       =>  'Operation state',
        'view_file'             =>  'View file',
        'message'               =>  'Message',
        'workflow_users'        =>  'workflow users',
        'fields'  => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Name',
            'name_helper'       => ' ',
            'created_at'        => 'Created at',
            'created_at_helper' => ' ',
            'updated_at'        => 'Updated at',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Deleted at',
            'deleted_at_helper' => ' ',
            'thumbnail'         => 'Thumbnail',
            'thumbnail_helper'  => ' ',
            'users'             => 'User(s)',
            'users_helper'      => ' ',
            'created_by'        => 'Created by',
            'updated_by'        => 'Updated by',
        ]
    ],

    'signature' => [
        'title'          => 'Signature',
        'title_singular' => 'Signature',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'signature_text'        => 'Signature',
            'signature_text_helper' => ' ',
            'owner'                 => 'Owner',
            'created_by'        => 'Created by',
        ],
    ],

    'parapheur' => [
        'title'          => 'Initial',
        'title_singular' => 'Initial',
    ],

    'archive' => [
        'title'          => 'Archive',
        'title_singular' => 'Archive',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'document'          => 'Document',
            'archive_text'      => 'Name',
            'archive_text_helper' => ' ',
            'file_type'         => 'file type',
            'size'              => 'Size',
            'state'             => 'State',
            'archive'           => 'archive',
            'visibility'        => 'visibility',
            'version'           => 'version',
            'created_by'        => 'Created by',
            'created_at'        => 'Created at',
            'updated_at'        => 'updated_at',
        ],
    ],
];
