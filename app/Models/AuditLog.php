<?php


namespace App\Models;


use \DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'properties' => 'collection',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
