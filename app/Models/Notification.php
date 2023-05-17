<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'notifiable_type', 'notifiable_id', 'data', 'read_at'];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function media() : BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function validationStep() : BelongsTo
    {
        return $this->belongsTo(ValidationStep::class);
    }
}
