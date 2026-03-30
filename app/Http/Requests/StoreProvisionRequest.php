<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProvisionRequest extends FormRequest
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
            'sci_id'         => ['required', 'exists:scis,id'],
            'month'          => ['required', 'integer', 'min:1', 'max:12'],
            'year'           => ['required', 'integer', 'min:2020'],
            'service_type'   => ['required', 'in:ELECTRICITE,PLOMBERIE,MENUISERIE,SERRURIE,VITRIER,CARRELAGE,PEINTURE,FERRONIER'],
            'agent'          => ['required', 'string', 'max:255'],
            'service_date'   => ['nullable', 'date'],
            'amount'         => ['nullable', 'numeric', 'min:0'],
            'status'         => ['nullable', 'in:En cours,Terminé,Ajourné'],
            'payment_method' => ['nullable', 'in:especes,virement,cheque,mobile_money,depot_bancaire'],
            'receipt'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sci_id.required'       => 'Veuillez sélectionner une SCI.',
            'sci_id.exists'         => 'La SCI sélectionnée est invalide.',
            'service_type.required' => 'Le type de prestation est obligatoire.',
            'service_type.in'       => 'Le type de prestation sélectionné est invalide.',
            'agent.required'        => 'Le nom de l\'agent est obligatoire.',
            'amount.numeric'        => 'Le montant doit être un nombre.',
            'amount.min'            => 'Le montant ne peut pas être négatif.',
            'receipt.mimes'         => 'Le justificatif doit être au format JPG, PNG ou PDF.',
            'receipt.max'           => 'Le justificatif ne doit pas dépasser 5 Mo.',
        ];
    }
}
