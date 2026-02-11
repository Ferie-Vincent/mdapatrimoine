<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequest extends FormRequest
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
            'channel'          => ['required', 'in:email,sms,whatsapp,courrier'],
            'message'          => ['nullable', 'string'],
            'level'            => ['nullable', 'integer', 'in:1,2,3'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lease_monthly_id.required' => 'L\'échéance mensuelle est obligatoire.',
            'lease_monthly_id.exists'   => 'L\'échéance mensuelle sélectionnée n\'existe pas.',
            'channel.required'          => 'Le canal de relance est obligatoire.',
            'channel.in'                => 'Le canal de relance sélectionné n\'est pas valide.',
            'message.required'          => 'Le message est obligatoire.',
            'message.string'            => 'Le message doit être une chaîne de caractères.',
        ];
    }
}
