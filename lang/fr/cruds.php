<?php

return [
    'userManagement' => [
        'title' => 'Gestion utilisateur'
    ],

    'permission' => [
        'title' => 'Permission',
        'title_singular' => 'permission',
        'fields'  => [
            'id' => 'ID',
            'title' => 'titre',
            'title_helper' => 'assistant de titre'
        ]
    ],
    'role' => [
        'title' => 'Role',
        'title_singular' => 'role',
        'fields'  => [
            'id' => 'ID',
            'title' => 'titre',
            'permissions' => 'permissions',
            'title_helper' => 'assistant de titre',
            'permissions_helper' => 'permissions helper',
        ]
    ],
    'user' => [
        'title' => 'Utilisateur',
        'title_singular' => 'utilisateur',
        'fields'  => [
            'id' => 'ID',
            'name' => 'nom',
            'email' => 'email',
            'email_helper' => 'assistant email',
            'password' => 'mot de passe',
            'password_helper' => 'assistant mot de passe',
            'roles_helper' => 'assistant role',
            'name_helper' => 'assistant nom',
            'email_verified_at' => 'email de vérification',
            'roles' => 'role',
        ]
    ],

    'project' => [
        'title' => 'Projet',
        'title_singular' => 'projet',
        'fields'  => [
            'id' => 'ID',
            'name' => 'nom',
            'users' => 'utilisateurs',
            'name_helper' => 'assistant nom',
            'users_helper' => 'assistant utilisateur',
        ]
    ],

    'folder' => [
        'title' => 'Dossier',
        'title_singular' => 'dossier',
        'fields'  => [
            'id' => 'ID',
            'name' => 'name',
            'project' => 'projet',
            'files' => 'fichier',
            'parent' => 'parent',
            'name_helper' => 'assistant nom',
            'project_helper' => 'assistant projet',
            'files_helper' => 'assistant fichier',
            'parent_helper' => 'assistant parent',
        ]
    ],
];
