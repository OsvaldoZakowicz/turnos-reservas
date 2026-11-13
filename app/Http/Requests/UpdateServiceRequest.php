<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'              => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'duration_minutes'  => ['required', 'integer', 'min:5', 'max:1440'],
            'price'             => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'is_active'         => ['required', 'boolean'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Debe indicar un nombre',
            'name.string' => 'Debe ser texto',
            'name.max' => 'Debe ser de hasta :max caracteres',
            'description.string' => 'Debe ser texto',
            'description.max' => 'Debe ser de hasta :max caracteres',
            'duration_minutes.required' => 'Una duracion es necesaria',
            'duration_minutes.min' => 'Debe ser de :min minutos como minimo',
            'duration_minutes.max' => 'Debe ser de :max minutos como maximo',
            'price.required' => 'Un precio es necesario',
            'price.numeric' => 'Debe ser un numero',
            'price.decimal' => 'Debe ser un decimal de hasta 2 caracteres despues del punto',
            'price.min' => 'Puede ser de costo :min como minimo',
            'is_active.required' => 'El estado es necesario'
        ];
    }
}
