<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class UpdateUserAlertRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('user_alert_edit');
    }

    public function rules()
    {
        return [];
    }
}
