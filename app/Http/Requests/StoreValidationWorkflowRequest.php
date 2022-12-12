<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;

class StoreValidationWorkflowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        abort_if(Gate::denies('validation_workflow_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['deadline' => "string[]", 'priority' => "string[]", 'visibility' => "string[]", 'workflow_sender' => "string[]", 'user_assign' => "string[]", 'media_id' => "string[]", 'message' => "string[]"])] public function rules(): array
    {
        return [
            'deadline'     => [
                'date',
                'nullable',
            ],
            'priority'    => [
                'string',
                'required',
            ],
            'visibility' => [
                'string',
                'required',
            ],
            'user_assign'    => [
                'integer',
                'required',
            ],
            'media_id'    => [
                'integer',
                'required',
            ],
            'message'    => [
                'string',
                'required',
            ],
        ];
    }
}
