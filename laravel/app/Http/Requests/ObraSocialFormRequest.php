<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObraSocialFormRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'descripcion'       =>  'required|between:3,100',
            'numero_seguro'     =>  'required'
        ];
    }
    public function messages(){
        return [
            'descripcion.required'  =>  'La :attribute es requerida',
            'descripcion.between'   =>  'La :attribute debe tener entre 3 y 100 caracteres',
            'numero_seguro.required'  =>  'El :attribute es requerida',
        ];
    }
    public function attributes(){
        return [
            'descripcion'   =>  'Descripcion',
            'numero_seguro' =>  'Numero de Seguro'
        ];
    }
}
