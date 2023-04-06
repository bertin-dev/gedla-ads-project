<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use PhpOffice\PhpWord\Media;

class Category extends Model
{
    use HasFactory, Auditable;

    public $table = 'categories';

    public $fillable = [
        'name',
        'description'
    ];

    //UNE CATEGORY CONTIENT 1 OU PLUSIEURS MEDIAS
    public function medias(): HasMany
    {
        return $this->hasMany(Media::class);
    }
}
