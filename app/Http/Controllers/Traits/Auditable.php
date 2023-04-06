<?php


namespace App\Http\Controllers\Traits;


use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            self::audit('audit:created', $model);
        });

        static::updated(function (Model $model) {
            $model->attributes = array_merge($model->getChanges(), ['id' => $model->id]);

            self::audit('audit:updated', $model);
        });

        static::deleted(function (Model $model) {
            self::audit('audit:deleted', $model);
        });
    }

    protected static function audit($description, $model)
    {
        AuditLog::create([
            'description'  => $description,
            'subject_id'   => $model->id ?? null,
            'subject_type' => null,
            'current_user_id'      => auth()->id() ?? null,
            'properties'   => $model ?? null,
            'host'         => request()->ip() ?? null,
        ]);
    }


    protected static function trackOperations(
        $mediaId = '',
        $operationType = '',
        $description = '',
        $operationState = '',
        $userIdSender = '',
        $userIdReceiver = '',
        $name = '',
        $message = '',
        $model = '')
    {
        AuditLog::create([
            'media_id'  => $mediaId,
            'operation_type'  => $operationType,
            'description'  => $description,
            'operation_state'  => $operationState,
            'user_id_sender'  => $userIdSender,
            'user_id_receiver'  => $userIdReceiver,
            'name'  => $name,
            'message'  => $message,
            'subject_id'   => $model->id ?? null,
            'subject_type' => is_object($model) ? sprintf('%s#%s', get_class($model), $model->id) ?? null : null,
            'current_user_id'      => auth()->id() ?? null,
            'properties'   => $model ?? null,
            'host'         => request()->ip() ?? null,
        ]);
    }
}
