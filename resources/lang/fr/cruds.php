<?php

return [
    'userManagement' => [
        'title'          => 'Gestion des utilisateurs',
        'title_singular' => 'Gestion des utilisateurs',
    ],
    'permission' => [
        'title'          => 'Permissions',
        'title_singular' => 'Permission',
        'fields'  => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'title'             => 'Titre',
            'title_helper'      => ' ',
            'created_at'        => 'Créer',
            'created_at_helper' => ' ',
            'updated_at'        => 'Modifier',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Supprimer',
            'deleted_at_helper' => ' ',
        ]
    ],
    'role' => [
        'title'          => 'Rôles',
        'title_singular' => 'Rôle',
        'fields'  => [
            'id'                 => 'ID',
            'id_helper'          => ' ',
            'title'              => 'Titre',
            'title_helper'       => ' ',
            'permissions'        => 'Permissions',
            'permissions_helper' => ' ',
            'created_at'         => 'Créer',
            'created_at_helper'  => ' ',
            'updated_at'         => 'Modifier',
            'updated_at_helper'  => ' ',
            'deleted_at'         => 'Supprimer',
            'deleted_at_helper'  => ' ',
        ]
    ],
    'user' => [
        'title'          => 'Utilisateurs',
        'title_singular' => 'Utilisateur',
        'fields'  => [
            'id'                       => 'ID',
            'id_helper'                => ' ',
            'name'                     => 'Nom',
            'name_helper'              => ' ',
            'email'                    => 'Email',
            'email_helper'             => ' ',
            'email_verified_at'        => 'Email vérifié à',
            'email_verified_at_helper' => ' ',
            'password'                 => 'Mot de passe',
            'password_helper'          => ' ',
            'roles'                    => 'Roles',
            'roles_helper'             => ' ',
            'remember_token'           => 'Se souvenir du jeton',
            'remember_token_helper'    => ' ',
            'created_at'               => 'Créer',
            'created_at_helper'        => ' ',
            'updated_at'               => 'Modifier',
            'updated_at_helper'        => ' ',
            'deleted_at'               => 'Supprimer',
            'deleted_at_helper'        => ' ',
        ]
    ],

    'project' => [
        'title'          => 'Entité',
        'title_singular' => 'Entité',
        'fields'  => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Nom',
            'name_helper'       => ' ',
            'created_at'        => 'Créer',
            'created_at_helper' => ' ',
            'updated_at'        => 'Modifier',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Supprimer',
            'deleted_at_helper' => ' ',
            'thumbnail'         => 'Thumbnail',
            'thumbnail_helper'  => ' ',
            'users'             => 'Utilisateur(s)',
            'users_helper' => ' ',
        ]
    ],

    'folder' => [
        'title'          => 'Dossier',
        'title_singular' => 'Dossier',
        'fields'  => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'name'              => 'Nom',
            'name_helper'       => ' ',
            'created_at'        => 'Créer',
            'created_at_helper' => ' ',
            'updated_at'        => 'Modifier',
            'updated_at_helper' => ' ',
            'deleted_at'        => 'Supprimer',
            'deleted_at_helper' => ' ',
            'parent'            => 'Parent',
            'parent_helper'     => ' ',
            'project'           => 'Entité',
            'project_helper'    => ' ',
            'files'             => 'Fichier(s)',
            'files_helper'      => ' ',
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
            'subject_id'          => 'ID Sujet',
            'subject_id_helper'   => ' ',
            'subject_type'        => 'Type de sujet',
            'subject_type_helper' => ' ',
            'user_id'             => 'ID Utilisateur',
            'user_id_helper'      => ' ',
            'properties'          => 'proprités',
            'properties_helper'   => ' ',
            'host'                => 'Invité',
            'host_helper'         => ' ',
            'created_at'          => 'Créer',
            'created_at_helper'   => ' ',
            'updated_at'          => 'Modifier',
            'updated_at_helper'   => ' ',
        ],
    ],

    'userAlert' => [
        'title'          => 'Alerte Utilisateur',
        'title_singular' => 'Alerte Utilisateur',
        'fields'         => [
            'id'                => 'ID',
            'id_helper'         => ' ',
            'alert_text'        => 'Texte d\'Alerte',
            'alert_text_helper' => ' ',
            'alert_link'        => 'Lien d\'Alerte',
            'alert_link_helper' => ' ',
            'user'              => 'Utilisateur',
            'user_helper'       => ' ',
            'created_at'        => 'Créer',
            'created_at_helper' => ' ',
            'updated_at'        => 'Modifier',
            'updated_at_helper' => ' ',
        ],
    ],
];
