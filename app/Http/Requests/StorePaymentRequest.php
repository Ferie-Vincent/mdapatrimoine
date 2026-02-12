<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\LeaseMonthly;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
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
            'lease_monthly_id' => ['required', 'exists:lease_monthlies,id'],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'paid_at'          => ['required', 'date'],
            'method'           => ['required', 'in:virement,especes,cheque,mobile_money,versement_especes,depot_bancaire,autre'],
            'reference'        => ['nullable', 'string'],
            'note'             => ['nullable', 'string'],
            'receipt'          => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    /**
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('lease_monthly_id') && $this->filled('amount')) {
                $monthly = LeaseMonthly::find($this->input('lease_monthly_id'));

                if ($monthly && (float) $this->input('amount') > (float) $monthly->remaining_amount) {
                    $validator->errors()->add(
                        'amount',
                        'Le montant du paiement (' . number_format((float) $this->input('amount'), 2, ',', ' ') .
                        ') ne doit pas dépasser le montant restant dû (' .
                        number_format((float) $monthly->remaining_amount, 2, ',', ' ') . ').'
                    );
                }
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lease_monthly_id.required' => 'L\'échéance mensuelle est obligatoire.',
            'lease_monthly_id.exists'   => 'L\'échéance mensuelle sélectionnée n\'existe pas.',
            'amount.required'           => 'Le montant est obligatoire.',
            'amount.numeric'            => 'Le montant doit être un nombre.',
            'amount.min'                => 'Le montant doit être d\'au moins 0,01.',
            'paid_at.required'          => 'La date de paiement est obligatoire.',
            'paid_at.date'              => 'La date de paiement n\'est pas valide.',
            'method.required'           => 'Le mode de paiement est obligatoire.',
            'method.in'                 => 'Le mode de paiement sélectionné n\'est pas valide.',
        ];
    }
}
