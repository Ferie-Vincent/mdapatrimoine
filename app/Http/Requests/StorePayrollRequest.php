<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayrollRequest extends FormRequest
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
            'staff_member_id' => ['required', 'exists:staff_members,id'],
            'sci_id'          => ['required', 'exists:scis,id'],
            'month'           => ['required', 'integer', 'min:1', 'max:12'],
            'year'            => ['required', 'integer', 'min:2020'],
            'amount'          => ['required', 'numeric', 'min:0'],
            'paid_at'         => ['required', 'date'],
            'payment_method'  => ['required', 'in:especes,virement,cheque,mobile_money,depot_bancaire'],
            'reference'       => ['nullable', 'string', 'max:255'],
            'note'            => ['nullable', 'string'],
            'receipt'         => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sci_id.required'          => 'Veuillez sélectionner une SCI.',
            'sci_id.exists'            => 'La SCI sélectionnée est invalide.',
            'staff_member_id.required' => 'Veuillez sélectionner un membre du personnel.',
            'staff_member_id.exists'   => 'Le membre du personnel sélectionné est invalide.',
            'amount.required'          => 'Le montant est obligatoire.',
            'amount.numeric'           => 'Le montant doit être un nombre.',
            'amount.min'               => 'Le montant ne peut pas être négatif.',
            'paid_at.required'         => 'La date de paiement est obligatoire.',
            'paid_at.date'             => 'La date de paiement n\'est pas valide.',
            'payment_method.required'  => 'Le mode de paiement est obligatoire.',
            'payment_method.in'        => 'Le mode de paiement sélectionné est invalide.',
            'receipt.mimes'            => 'Le justificatif doit être au format JPG, PNG ou PDF.',
            'receipt.max'              => 'Le justificatif ne doit pas dépasser 5 Mo.',
        ];
    }
}
