<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email,' . $this->route('user')->id],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'      => ['required', 'in:super_admin,gestionnaire,lecture_seule'],
            'is_active' => ['boolean'],
            'sci_ids'   => ['required_unless:role,super_admin', 'array'],
            'sci_ids.*' => ['exists:scis,id'],
            'avatar'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'          => 'Le nom est obligatoire.',
            'name.string'            => 'Le nom doit être une chaîne de caractères.',
            'name.max'               => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required'         => 'L\'adresse e-mail est obligatoire.',
            'email.email'            => 'L\'adresse e-mail n\'est pas valide.',
            'email.unique'           => 'Cette adresse e-mail est déjà utilisée.',
            'password.string'        => 'Le mot de passe doit être une chaîne de caractères.',
            'password.min'           => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'     => 'La confirmation du mot de passe ne correspond pas.',
            'role.required'          => 'Le rôle est obligatoire.',
            'role.in'                => 'Le rôle sélectionné n\'est pas valide.',
            'is_active.boolean'      => 'Le statut actif doit être vrai ou faux.',
            'sci_ids.required_unless' => 'Les SCIs sont obligatoires pour ce rôle.',
            'sci_ids.array'          => 'Les SCIs doivent être un tableau.',
            'sci_ids.*.exists'       => 'La SCI sélectionnée n\'existe pas.',
            'avatar.image'           => 'L\'avatar doit être une image.',
            'avatar.mimes'           => 'L\'avatar doit être au format JPG, PNG ou WebP.',
            'avatar.max'             => 'L\'avatar ne doit pas dépasser 2 Mo.',
        ];
    }
}
