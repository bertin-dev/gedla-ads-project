<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PhpOffice\PhpWord\Media;

class Category extends Model
{
    use HasFactory;

    public $table = 'categories';

    protected $fillable = [
        'name',
        'description'
    ];

    //UNE CATEGORY CONTIENT 1 OU PLUSIEURS MEDIAS
    public function medias(): HasMany
    {
        return $this->hasMany(Media::class);
    }
}
