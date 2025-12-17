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
            'items.*.producto_id' => ['required', 'integer', 'exists:productos,id'],
            'items.*.cantidad' => ['required', 'numeric', 'min:1'],
            'items.*.precio_unitario' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'Se requiere al menos un item en la factura.',
            'items.*.producto_id.exists' => 'El producto especificado no existe. Verifica que el ID sea correcto.',
            'items.*.producto_id.required' => 'El ID del producto es requerido en cada item.',
            'items.*.cantidad.required' => 'La cantidad es requerida en cada item.',
            'items.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
            'items.*.precio_unitario.required' => 'El precio unitario es requerido en cada item.',
            'items.*.precio_unitario.min' => 'El precio unitario no puede ser negativo.',
        ];
    }
}
