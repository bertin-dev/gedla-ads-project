<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use \DateTimeInterface;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use SoftDeletes, Notifiable, HasApiTokens, Auditable, InteractsWithMedia;

    public $table = 'users';

    public static $searchable = [
        'name',
        'email',
    ];

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'email',
        'user_state',
        'created_by',
        'email_verified_at',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
        'last_login_at',
        'last_login_ip_address'
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->roles()->where('id', 1)->exists();
    }

    public function getEmailVerifiedAtAttribute($value): ?string
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function userUserAlerts(): BelongsToMany
    {
        return $this->belongsToMany(UserAlert::class);
    }



    //un utilisateur peut attribuer plusieurs permissions à plusieurs personnes
    public function permissionsBy(): HasMany
    {
        return $this->hasMany(Permission::class);
    }

    //un utilisateur peut attribuer plusieurs rôles à plusieurs personnes
    public function rolesBy(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    //USER-ALERT
    public function userAlertsBy(): HasMany
    {
        return $this->hasMany(UserAlert::class);
    }


    //un utilisateur peut être associé à plusieurs dossiers d\'un repertoire
    public function userFoldersBy(): HasMany
    {
        return $this->hasMany(Folder::class);
    }

    //un utilisateur peut appartenir à plusieurs projets
    public function userProjectsBy(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    //PARENT USER
    public function parentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //CHILDREN USERS CREATED BY PARENT
    public function childrenUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    //UN UTILISATEUR PEUT EFFECTUER PLUSIEURS LOG
    public function sendLog(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id_sender');
    }

    //UN UTILISATEUR PEUT RECEPTIONNER UN OU PLUSIEURS LOG
    public function receiveLog(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id_receiver');
    }

    //UN UTILISATEUR PEUT SAUVEGARDER UN OU PLUSIEURS LOG
    public function saveLog(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'current_user_id');
    }

    //un utilisateur peut avoir signer plusieurs media
    public function medias(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    //UN UTILISATEUR APPARTIENT A PLUSIEURS DOSSIERS
    public function multiFolders(): BelongsToMany
    {
        return $this->belongsToMany(Folder::class, 'folder_user', 'user_id', 'folder_id');
    }

}
