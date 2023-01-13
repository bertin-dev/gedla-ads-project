<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Parapheur extends Model implements HasMedia
{
    //use HasFactory;
    use SoftDeletes, InteractsWithMedia, Auditable;

    public $table = 'parapheurs';

    public $fillable = [
      'name',
      'description',
      'project_id',
      'media_id',
      'user_id',
    ];

    //UN PARAPHEUR APPARTIENT A UN SEUL PROJET
    public function projet(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    //UN PARAPHEUR PEUT CONTENIR 1 OU PLUSIEURS FICHIERS
    public function medias(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    //A partir d'un parapheur on peu retrouver son propriétaire
    public function getUser(): HasOne
    {
        return $this->HasOne(User::class, 'user_id', 'id');
    }

}
