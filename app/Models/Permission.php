<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Permission extends Model
{
    use SoftDeletes, Auditable;

    public $table = 'permissions';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
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

    //CREATED BY
    public function userCreatedPermissionBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //UPDATED BY
    public function userUpdatedPermissionBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
