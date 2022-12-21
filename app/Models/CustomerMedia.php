<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CustomerMedia extends Media
{
    use Auditable;
    use HasFactory;

    protected $fillable = [
        'media_version',
        'media_status',
        'media_state',
        'media_signing',
        'media_save',
        'created_by',
        'signed_by'
    ];

    //CREATED BY
    public function userCreatedCustomerMediaBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //SIGNED BY
    public function userSignedCustomerMediaBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    //UN MEDIA PEUT AVOIR UN OU PLUSIEURS OPERATIONS
    public function workflowValidates(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    //UN MEDIA APPARTIENT A UNE SEUL CATEGORY
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
