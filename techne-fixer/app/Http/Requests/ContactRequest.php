<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255'],
            'phone'           => ['nullable', 'string', 'max:30'],
            'service'         => ['required', 'string', 'in:web-development,mobile-development,ui-ux-design,cloud-solutions,maintenance,devops'],
            'message'         => ['required', 'string', 'min:10', 'max:5000'],
            'recaptcha_token' => ['required', 'string'],
        ];
    }
}