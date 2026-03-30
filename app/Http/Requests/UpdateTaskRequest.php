<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'title'          => ['sometimes', 'required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'category'       => ['sometimes', 'required', 'in:encaissement,relance,visite,administratif,document,autre'],
            'related_type'   => ['nullable', 'string', 'in:App\\Models\\LeaseMonthly,App\\Models\\Lease,App\\Models\\Tenant,App\\Models\\Property'],
            'related_id'     => ['nullable', 'integer', 'required_with:related_type'],
            'amount'         => ['nullable', 'numeric', 'min:0'],
            'priority'       => ['sometimes', 'required', 'in:haute,moyenne,basse'],
            'scheduled_date' => ['sometimes', 'required', 'date'],
            'scheduled_time' => ['nullable', 'date_format:H:i'],
            'reminder_at'    => ['nullable', 'date'],
            'user_id'        => ['nullable', 'exists:users,id'],
            'recurrence'     => ['nullable', 'in:quotidien,hebdomadaire,mensuel,trimestriel,annuel'],
            'recurrence_end_date' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required'          => 'Le titre est obligatoire.',
            'title.max'               => 'Le titre ne peut pas dépasser 255 caractères.',
            'category.in'             => 'La catégorie sélectionnée n\'est pas valide.',
            'priority.in'             => 'La priorité sélectionnée n\'est pas valide.',
            'scheduled_date.date'     => 'La date n\'est pas valide.',
            'amount.numeric'          => 'Le montant doit être un nombre.',
            'amount.min'              => 'Le montant ne peut pas être négatif.',
            'user_id.exists'          => 'L\'utilisateur sélectionné n\'existe pas.',
            'recurrence.in'           => 'La periodicite selectionnee n\'est pas valide.',
        ];
    }
}
