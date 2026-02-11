<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
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
            'sci_id'                  => ['required', 'exists:scis,id'],
            'first_name'              => ['required', 'string', 'max:255'],
            'last_name'               => ['required', 'string', 'max:255'],
            'email'                   => ['nullable', 'email'],
            'phone'                   => ['required', 'string'],
            'phone_secondary'         => ['nullable', 'string'],
            'whatsapp_phone'          => ['nullable', 'string', 'max:20'],
            'address'                 => ['nullable', 'string'],
            'id_type'                 => ['nullable', 'string'],
            'id_number'               => ['nullable', 'string'],
            'id_expiration'           => ['nullable', 'date'],
            'id_file'                 => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'id_file_verso'           => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'payment_receipt'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'profession'              => ['nullable', 'string'],
            'employer'                => ['nullable', 'string'],
            'emergency_contact_name'  => ['nullable', 'string'],
            'emergency_contact_phone' => ['nullable', 'string'],
            'guarantor_name'          => ['nullable', 'string'],
            'guarantor_phone'         => ['nullable', 'string'],
            'guarantor_address'       => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sci_id.required'        => 'La SCI est obligatoire.',
            'sci_id.exists'          => 'La SCI sélectionnée n\'existe pas.',
            'first_name.required'    => 'Le prénom est obligatoire.',
            'first_name.string'      => 'Le prénom doit être une chaîne de caractères.',
            'first_name.max'         => 'Le prénom ne doit pas dépasser :max caractères.',
            'last_name.required'     => 'Le nom est obligatoire.',
            'last_name.string'       => 'Le nom doit être une chaîne de caractères.',
            'last_name.max'          => 'Le nom ne doit pas dépasser :max caractères.',
            'email.email'            => 'L\'adresse e-mail n\'est pas valide.',
            'phone.required'         => 'Le numéro de téléphone est obligatoire.',
            'phone.string'           => 'Le numéro de téléphone doit être une chaîne de caractères.',
            'id_expiration.date'     => 'La date d\'expiration de la pièce d\'identité n\'est pas valide.',
            'id_file.file'           => 'Le fichier d\'identité doit être un fichier.',
            'id_file.mimes'          => 'Le fichier d\'identité doit être au format PDF, JPG, JPEG ou PNG.',
            'id_file.max'            => 'Le fichier d\'identité ne doit pas dépasser 5 Mo.',
            'id_file_verso.file'     => 'Le verso doit être un fichier.',
            'id_file_verso.mimes'    => 'Le verso doit être au format PDF, JPG, JPEG ou PNG.',
            'id_file_verso.max'      => 'Le verso ne doit pas dépasser 5 Mo.',
            'payment_receipt.file'   => 'Le justificatif de paiement doit être un fichier.',
            'payment_receipt.mimes'  => 'Le justificatif doit être au format PDF, JPG, JPEG ou PNG.',
            'payment_receipt.max'    => 'Le justificatif ne doit pas dépasser 5 Mo.',
        ];
    }
}
