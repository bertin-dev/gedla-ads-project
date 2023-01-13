<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Operation extends Model
{
    use HasFactory;
    use Auditable;

    public $table = 'operations';

    protected $fillable = [
        'deadline',
        'operation_type',
        'priority',
        'status',
        'user_id_sender',
        'user_id_receiver',
        'media_id',
        'message',
        'operation_state',
        'num_operation',
        'receive_mail_notification',
        'receiver_read_doc',
        'receiver_read_doc_at',
    ];

    /*NOUS POURRIONS EGALEMENT IMPLEMENTER LA POSSIBILITE QUE LA VALIDATION DU WORKFLOW SOIT EFFECTUE PAR 1 OU
    PLUSIEURS UTILISATEURS SIMULTANÉMENT*/

    //UNE OPERATION EST EFFECTUE PAR 1 SEUL UTILISATEUR.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    //L' OPERATION CONCERNE 1 SEUL MEDIA A LA FOIS
    public function mediaFile(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
