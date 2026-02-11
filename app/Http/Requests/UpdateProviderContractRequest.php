<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProviderContractRequest extends FormRequest
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
            'service_provider_id' => ['required', 'exists:service_providers,id'],
            'sci_id'              => ['required', 'exists:scis,id'],
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'amount'              => ['required', 'numeric', 'min:0'],
            'start_date'          => ['required', 'date'],
            'end_date'            => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'              => ['required', 'in:actif,termine,annule'],
            'document_path'       => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'service_provider_id.required' => 'Le prestataire est obligatoire.',
            'title.required'               => 'Le titre du contrat est obligatoire.',
            'amount.required'              => 'Le montant est obligatoire.',
            'start_date.required'          => 'La date de debut est obligatoire.',
            'end_date.after_or_equal'      => 'La date de fin doit etre posterieure a la date de debut.',
            'status.in'                    => 'Le statut selectionne n\'est pas valide.',
        ];
    }
}
