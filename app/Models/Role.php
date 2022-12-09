<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Role extends Model
{
    use SoftDeletes;
    use Auditable;

    public $table = 'roles';

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

    public function permissions() : BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    //CREATED BY
    public function userCreatedRoleBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //UPDATED_AT
    public function userUpdatedRoleBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
