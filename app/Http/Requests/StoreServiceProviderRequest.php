<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceProviderRequest extends FormRequest
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
            'sci_id'           => ['required', 'exists:scis,id'],
            'name'             => ['required', 'string', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:50'],
            'phone_secondary'  => ['nullable', 'string', 'max:50'],
            'email'            => ['nullable', 'email', 'max:255'],
            'category'         => ['required', 'in:artisan,manoeuvre,plombier,electricien,peintre,menuisier,macon,serrurier,climatiseur,autre'],
            'custom_category'  => ['nullable', 'required_if:category,autre', 'string', 'max:255'],
            'specialty'        => ['nullable', 'string', 'max:255'],
            'company'          => ['nullable', 'string', 'max:255'],
            'address'          => ['nullable', 'string', 'max:500'],
            'notes'            => ['nullable', 'string'],
            'is_active'        => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sci_id.required'          => 'La SCI est obligatoire.',
            'sci_id.exists'            => 'La SCI selectionnee n\'existe pas.',
            'name.required'            => 'Le nom est obligatoire.',
            'name.max'                 => 'Le nom ne peut pas depasser 255 caracteres.',
            'email.email'              => 'L\'adresse email n\'est pas valide.',
            'category.required'        => 'La categorie est obligatoire.',
            'category.in'              => 'La categorie selectionnee n\'est pas valide.',
            'custom_category.required_if' => 'Veuillez preciser la categorie personnalisee.',
        ];
    }
}
