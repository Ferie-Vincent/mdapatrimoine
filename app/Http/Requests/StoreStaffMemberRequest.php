<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isSuperAdmin() || $this->user()->isGestionnaire();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sci_id'      => ['required', 'exists:scis,id'],
            'first_name'  => ['required', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'role'        => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'email'       => ['nullable', 'email', 'max:255'],
            'address'     => ['nullable', 'string', 'max:500'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'hire_date'   => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sci_id.required'     => 'Veuillez sélectionner une SCI.',
            'sci_id.exists'       => 'La SCI sélectionnée est invalide.',
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required'  => 'Le nom est obligatoire.',
            'email.email'         => 'L\'adresse email n\'est pas valide.',
            'base_salary.numeric' => 'Le salaire doit être un nombre.',
            'base_salary.min'     => 'Le salaire ne peut pas être négatif.',
        ];
    }
}
