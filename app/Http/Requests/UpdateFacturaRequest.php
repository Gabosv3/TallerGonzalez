<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacturaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'estado' => ['nullable', 'string', 'in:pendiente,pagada,cancelada'],
            'pago' => ['nullable', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'estado.in' => 'Estado inválido. Valores permitidos: pendiente, pagada, cancelada.',
        ];
    }
}
