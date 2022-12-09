<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use \DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserAlert extends Model
{
    use HasFactory;
    use Auditable;

    public $table = 'user_alerts';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'alert_text',
        'alert_link',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    //CREATED BY
    public function userCreatedAlertBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //UPDATED BY
    public function userUpdatedAlertBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
