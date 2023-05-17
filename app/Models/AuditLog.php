<?php


namespace App\Models;


use \DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AuditLog extends Model
{
    public $table = 'audit_logs';

    protected $fillable = [
        'media_id',
        'operation_type',
        'description',
        'operation_state',
        'user_id_sender',
        'user_id_receiver',
        'subject_id',
        'subject_type',
        'current_user_id',
        'properties',
        'host',
        'message',
    ];

    protected $casts = [
        'properties' => 'collection',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    //UN AUDITLOG EST EFFECTUE PAR 1 SEUL UTILISATEUR SENDER.
    public function senderUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_sender');
    }

    //UN AUDITLOG EST EFFECTUE PAR 1 SEUL UTILISATEUR RECEIVER.
    public function receiverUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_receiver');
    }

    public function currentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }

    //UN AUDITLOG CONCERNE 1 SEUL MEDIA A LA FOIS
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
