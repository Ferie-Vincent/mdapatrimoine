<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'category'       => ['required', 'in:encaissement,relance,visite,administratif,document,autre'],
            'related_type'   => ['nullable', 'string', 'in:App\\Models\\LeaseMonthly,App\\Models\\Lease,App\\Models\\Tenant,App\\Models\\Property'],
            'related_id'     => ['nullable', 'integer', 'required_with:related_type'],
            'amount'         => ['nullable', 'numeric', 'min:0'],
            'priority'       => ['required', 'in:haute,moyenne,basse'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['nullable', 'date_format:H:i'],
            'reminder_at'    => ['nullable', 'date', 'after_or_equal:now'],
            'user_id'        => ['nullable', 'exists:users,id'],
            'recurrence'     => ['nullable', 'in:quotidien,hebdomadaire,mensuel,trimestriel,annuel'],
            'recurrence_end_date' => ['nullable', 'date', 'after:scheduled_date'],
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
            'category.required'       => 'La catégorie est obligatoire.',
            'category.in'             => 'La catégorie sélectionnée n\'est pas valide.',
            'priority.required'       => 'La priorité est obligatoire.',
            'priority.in'             => 'La priorité sélectionnée n\'est pas valide.',
            'scheduled_date.required' => 'La date est obligatoire.',
            'scheduled_date.date'     => 'La date n\'est pas valide.',
            'amount.numeric'          => 'Le montant doit être un nombre.',
            'amount.min'              => 'Le montant ne peut pas être négatif.',
            'reminder_at.after_or_equal' => 'Le rappel doit être dans le futur.',
            'related_id.required_with'   => 'L\'identifiant de l\'entité liée est requis.',
            'user_id.exists'          => 'L\'utilisateur sélectionné n\'existe pas.',
            'recurrence.in'           => 'La periodicite selectionnee n\'est pas valide.',
            'recurrence_end_date.after' => 'La date de fin de repetition doit etre apres la date de la tache.',
        ];
    }
}
