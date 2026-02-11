<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSciRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'      => ['sometimes', 'required', 'string', 'max:255'],
            'rccm'      => ['nullable', 'string'],
            'ifu'       => ['nullable', 'string'],
            'address'   => ['nullable', 'string'],
            'phone'     => ['nullable', 'string'],
            'email'     => ['nullable', 'email'],
            'bank_name' => ['nullable', 'string'],
            'bank_iban' => ['nullable', 'string'],
            'logo'      => ['nullable', 'file', 'image', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'  => 'Le nom de la SCI est obligatoire.',
            'name.string'    => 'Le nom doit être une chaîne de caractères.',
            'name.max'       => 'Le nom ne doit pas dépasser :max caractères.',
            'email.email'    => 'L\'adresse e-mail n\'est pas valide.',
            'logo.file'      => 'Le logo doit être un fichier.',
            'logo.image'     => 'Le logo doit être une image (jpeg, png, bmp, gif, svg ou webp).',
            'logo.max'       => 'Le logo ne doit pas dépasser 2 Mo.',
        ];
    }
}
