<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeaseRequest extends FormRequest
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
            'sci_id'              => ['required', 'exists:scis,id'],
            'property_id'         => ['sometimes', 'required', 'exists:properties,id'],
            'tenant_id'           => ['sometimes', 'required', 'exists:tenants,id'],
            'start_date'          => ['required', 'date'],
            'end_date'            => ['nullable', 'date', 'after:start_date'],
            'duration_months'     => ['nullable', 'integer', 'min:1'],
            'rent_amount'         => ['required', 'numeric', 'min:0'],
            'charges_amount'      => ['nullable', 'numeric', 'min:0'],
            'deposit_amount'      => ['nullable', 'numeric', 'min:0'],
            'payment_method'      => ['required', 'in:virement,especes,cheque,mobile_money,autre'],
            'due_day'             => ['required', 'integer', 'min:1', 'max:28'],
            'penalty_rate'        => ['nullable', 'numeric', 'min:0'],
            'penalty_delay_days'  => ['nullable', 'integer', 'min:0'],
            'status'              => ['required', 'in:actif,en_attente'],
            'signed_lease'        => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'entry_inspection'    => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'notes'               => ['nullable', 'string'],
            // Excel fields
            'dossier_number'          => ['nullable', 'string', 'max:255'],
            'agency_name'             => ['nullable', 'string', 'max:255'],
            'entry_inventory_date'    => ['nullable', 'date'],
            'caution_2_mois'          => ['nullable', 'numeric', 'min:0'],
            'loyers_avances_2_mois'   => ['nullable', 'numeric', 'min:0'],
            'frais_agence'            => ['nullable', 'numeric', 'min:0'],
            'notice_deposit_date'     => ['nullable', 'date'],
            'exit_inventory_date'     => ['nullable', 'date'],
            'actual_exit_date'        => ['nullable', 'date'],
            'charges_due_amount'      => ['nullable', 'numeric', 'min:0'],
            'deposit_returned_amount' => ['nullable', 'numeric', 'min:0'],
            'debts_or_credits_note'   => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sci_id.required'             => 'La SCI est obligatoire.',
            'sci_id.exists'               => 'La SCI sélectionnée n\'existe pas.',
            'property_id.required'        => 'Le bien immobilier est obligatoire.',
            'property_id.exists'          => 'Le bien immobilier sélectionné n\'existe pas.',
            'tenant_id.required'          => 'Le locataire est obligatoire.',
            'tenant_id.exists'            => 'Le locataire sélectionné n\'existe pas.',
            'start_date.required'         => 'La date de début est obligatoire.',
            'start_date.date'             => 'La date de début n\'est pas valide.',
            'end_date.date'               => 'La date de fin n\'est pas valide.',
            'end_date.after'              => 'La date de fin doit être postérieure à la date de début.',
            'duration_months.integer'     => 'La durée doit être un nombre entier.',
            'duration_months.min'         => 'La durée doit être d\'au moins 1 mois.',
            'rent_amount.required'        => 'Le montant du loyer est obligatoire.',
            'rent_amount.numeric'         => 'Le montant du loyer doit être un nombre.',
            'rent_amount.min'             => 'Le montant du loyer ne peut pas être négatif.',
            'charges_amount.numeric'      => 'Le montant des frais d\'agence doit être un nombre.',
            'charges_amount.min'          => 'Le montant des frais d\'agence ne peut pas être négatif.',
            'deposit_amount.numeric'      => 'Le montant du dépôt de garantie doit être un nombre.',
            'deposit_amount.min'          => 'Le montant du dépôt de garantie ne peut pas être négatif.',
            'payment_method.required'     => 'Le mode de paiement est obligatoire.',
            'payment_method.in'           => 'Le mode de paiement sélectionné n\'est pas valide.',
            'due_day.required'            => 'Le jour d\'échéance est obligatoire.',
            'due_day.integer'             => 'Le jour d\'échéance doit être un nombre entier.',
            'due_day.min'                 => 'Le jour d\'échéance doit être au minimum 1.',
            'due_day.max'                 => 'Le jour d\'échéance doit être au maximum 28.',
            'penalty_rate.numeric'        => 'Le taux de pénalité doit être un nombre.',
            'penalty_rate.min'            => 'Le taux de pénalité ne peut pas être négatif.',
            'penalty_delay_days.integer'  => 'Le délai de pénalité doit être un nombre entier.',
            'penalty_delay_days.min'      => 'Le délai de pénalité ne peut pas être négatif.',
            'status.required'             => 'Le statut est obligatoire.',
            'status.in'                   => 'Le statut sélectionné n\'est pas valide.',
            'signed_lease.file'           => 'Le bail signé doit être un fichier.',
            'signed_lease.mimes'          => 'Le bail signé doit être au format PDF.',
            'signed_lease.max'            => 'Le bail signé ne doit pas dépasser 10 Mo.',
            'entry_inspection.file'       => 'L\'état des lieux d\'entrée doit être un fichier.',
            'entry_inspection.mimes'      => 'L\'état des lieux d\'entrée doit être au format PDF, JPG, JPEG ou PNG.',
            'entry_inspection.max'        => 'L\'état des lieux d\'entrée ne doit pas dépasser 10 Mo.',
        ];
    }
}
