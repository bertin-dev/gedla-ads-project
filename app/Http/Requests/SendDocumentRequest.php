<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Response;

class SendDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        //abort_if(Gate::denies('send_document'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['deadline' => "string[]", 'user_assign' => "string[]", 'media_id' => "string[]", 'message' => "string[]"])] public function rules(): array
    {
        return [
            'user_assign' => [
                'integer',
                'required',
            ],
            'media_id' => [
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
