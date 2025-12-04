<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFacturaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'numero_factura' => ['nullable', 'string', 'max:255', Rule::unique('facturas', 'numero_factura')],
            'fecha' => ['nullable', 'date'],
            'cliente_id' => ['nullable', 'exists:clientes,id'],
            'cliente' => ['required_without:cliente_id', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.producto_id' => ['required', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'numeric', 'min:1'],
            'items.*.precio_unitario' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'Se requiere al menos un item en la factura.',
            'items.*.producto_id.exists' => 'El producto especificado no existe.',
        ];
    }
}
