<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Folder extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, Auditable;

    public $table = 'folders';

    protected $appends = [
        'files',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'description',
        'project_id',
        'parent_id',
        'thumbnail_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'folder_access'
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }


    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function getFilesAttribute(): MediaCollection
    {
        $files = $this->getMedia('files');

        $files->map(function ($file) {
            $file->thumbnail = substr($file->mime_type, 0, 5) == 'image' ? $file->getUrl('thumb') : null;
        });

        return $files;
    }

    public function getImagesAttribute()
    {
        return $this->files->filter(function ($file) {
            return substr($file->mime_type, 0, 5) == 'image';
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children() : HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    /*-----------------------------------------------------*/


    public function subChildren(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id')->with('children');
    }


    /*public function scopeFindByParentId($query, $parentId){
        return $query->where('parent_id', $parentId);
    }*/


    //CREATED BY
    public function userCreatedFolderBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //UPDATED_BY
    public function userUpdatedFolderBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


    //UN DOSSIER PEUT-ETRE UTILISE PAR PLUSIEURS UTILISATEURS
    public function multiUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'folder_user', 'folder_id', 'user_id')
            ->withPivot('user_id');
    }
}
