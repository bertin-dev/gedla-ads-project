<?php

namespace Database\Seeders;

use App\Models\Folder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoldersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $functionality = [
            [
                'id'            => 1,
                'name'          => 'ocr',
                'description'   => 'Optical character recognized',
                'functionality' => true,
                'created_by'    => 1
            ],
            [
                'id'            => 2,
                'name'          => 'page',
                'description'   => '',
                'functionality' => true,
                'created_by'    => 1
            ],
            [
                'id'            => 3,
                'name'          => 'parapheur',
                'description'   => 'Recept All documents',
                'functionality' => true,
                'created_by'    => 1
            ],
            [
                'id'            => 4,
                'name'          => 'safe',
                'description'   => 'Save all documents',
                'functionality' => true,
                'created_by'    => 1
            ],
            [
                'id'            => 5,
                'name'          => 'launch',
                'description'   => '',
                'functionality' => true,
                'created_by'    => 1
            ],
            [
                'id'            => 6,
                'name'          => 'dispath',
                'description'   => 'Recept All documents',
                'functionality' => true,
                'created_by'    => 1
            ],
            [
                'id'            => 7,
                'name'          => 'design',
                'description'   => '',
                'functionality' => true,
                'created_by'    => 1
            ],
        ];

        Folder::insert($functionality);
    }
}
