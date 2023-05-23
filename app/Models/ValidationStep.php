<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ValidationStep extends Model
{
    use HasFactory, Auditable;

    public $table = 'validation_steps';

    protected $fillable = [
        'media_id',
        'user_id',
        'statut',
        'deadline',
        'order',
        'date_validation',
        'start_workflow_by'
    ];

    //la validation concerne un et un seul media
    public function media() : BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    //la validation est effectué par un utilisateur à chaque étape
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //Notifications
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
