<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
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
            'sci_id' => ['required', 'exists:scis,id'],
            'month'  => ['required', 'integer', 'min:1', 'max:12'],
            'year'   => ['required', 'integer', 'min:2020'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sci_id.required' => 'Veuillez sélectionner une SCI.',
            'sci_id.exists'   => 'La SCI sélectionnée est invalide.',
            'amount.required' => 'Le montant du budget est obligatoire.',
            'amount.numeric'  => 'Le montant doit être un nombre.',
            'amount.min'      => 'Le montant ne peut pas être négatif.',
        ];
    }
}
