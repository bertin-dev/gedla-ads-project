<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use Illuminate\Testing\Fluent\Concerns\Has;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Project extends Model
{
    use SoftDeletes;
    use Auditable;

    public $table = 'projects';

    public static $searchable = [
        'name',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'thumbnail_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function folders() : HasMany
    {
        return $this->hasMany(Folder::class);
    }

    public function parentDirectory() : HasOne
    {
        return $this->hasOne(Folder::class)->whereNull('parent_id');
    }

    public function getFilesAttribute()
    {
        if ($this->parentDirectory) {
            return $this->parentDirectory->files;
        }

        return collect();
    }

    public function getImagesAttribute()
    {
        if ($this->parentDirectory) {
            return $this->parentDirectory->images;
        }

        return collect();
    }

    public function getThumbnailAttribute(): Media
    {
        if ($this->thumbnail_id) {
            $image = Media::firstWhere('id', $this->thumbnail_id);
            $image->thumbnail = substr($image->mime_type, 0, 5) == 'image' ? $image->getUrl('thumb') : null;
        } else {
            $image = $this->images->first();
        }

        return $image;
    }


    //CREATED BY
    public function userCreatedProjectBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //UPDATED_BY
    public function userUpdatedProjectBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    //UN PROJET CONTIENT 1 OU PLUSIEURS PARAPHEURS
    public function multiParapheurs(): HasMany
    {
        return $this->hasMany(Parapheur::class);
    }
}
