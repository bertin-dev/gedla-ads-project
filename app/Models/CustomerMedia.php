<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'updated_by',
        'signed_by'
    ];

    //CREATED BY
    public function userCreatedCustomerMediaBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //UPDATED BY
    public function userUpdatedCustomerMediaBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    //SIGNED BY
    public function userSignedCustomerMediaBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signed_by');
    }
}
