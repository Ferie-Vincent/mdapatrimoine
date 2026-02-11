<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
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
            'sci_id'            => ['required', 'exists:scis,id'],
            'reference'         => ['required', 'string', 'unique:properties,reference'],
            'type'              => ['required', 'in:appartement,maison,studio,bureau,commerce,terrain,autre'],
            'address'           => ['required', 'string'],
            'city'              => ['nullable', 'string'],
            'description'       => ['nullable', 'string'],
            'surface'           => ['nullable', 'numeric', 'min:0'],
            'rooms'             => ['nullable', 'integer', 'min:0'],
            'status'            => ['required', 'in:disponible,occupe,travaux'],
            'niveau'            => ['nullable', 'string', 'max:50'],
            'numero_porte'      => ['nullable', 'string', 'max:50'],
            'nb_keys'           => ['nullable', 'integer'],
            'nb_clim'           => ['nullable', 'integer'],
            'cie_meter_number'  => ['nullable', 'string', 'max:255'],
            'sodeci_meter_number' => ['nullable', 'string', 'max:255'],
            'latitude'            => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'           => ['nullable', 'numeric', 'between:-180,180'],
            'photos'              => ['nullable', 'array', 'max:10'],
            'photos.*'            => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sci_id.required'            => 'La SCI est obligatoire.',
            'sci_id.exists'              => 'La SCI sélectionnée n\'existe pas.',
            'reference.required'         => 'La référence du bien est obligatoire.',
            'reference.string'           => 'La référence doit être une chaîne de caractères.',
            'reference.unique'           => 'Cette référence est déjà utilisée par un autre bien.',
            'type.required'              => 'Le type de bien est obligatoire.',
            'type.in'                    => 'Le type de bien sélectionné n\'est pas valide.',
            'address.required'           => 'L\'adresse est obligatoire.',
            'address.string'             => 'L\'adresse doit être une chaîne de caractères.',
            'surface.numeric'            => 'La surface doit être un nombre.',
            'surface.min'                => 'La surface ne peut pas être négative.',
            'rooms.integer'              => 'Le nombre de pièces doit être un entier.',
            'rooms.min'                  => 'Le nombre de pièces ne peut pas être négatif.',
            'status.required'            => 'Le statut est obligatoire.',
            'status.in'                  => 'Le statut sélectionné n\'est pas valide.',
        ];
    }
}
