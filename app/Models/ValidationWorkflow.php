<?php

namespace App\Models;

use App\Http\Controllers\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PhpOffice\PhpWord\Media;

class ValidationWorkflow extends Model
{
    use HasFactory;
    use Auditable;

     public $table = 'validation_workflows';

    protected $fillable = [
      'deadline',
      'priority',
      'status',
      'workflow_sender',
      'workflow_receiver',
      'media_id',
      'message',
      'receive_mail_notification'
    ];

    /*NOUS POURRIONS EGALEMENT IMPLEMENTER LA POSSIBILITE QUE LA VALIDATION DU WORKFLOW SOIT EFFECTUE PAR 1 OU
    PLUSIEURS UTILISATEURS SIMULTANÉMENT*/

    //A L'ETAT ACTUEL LA VALIDATION DU WORKFLOW EST EFFECTUE PAR UN UTILISATEUR A LA FOIS.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    //LA VALIDATION DU WORKFLOW CONCERNE UN ET UN SEUL MEDIA A LA FOIS.
    public function mediaFile(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
