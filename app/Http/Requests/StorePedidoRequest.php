<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero_factura' => [
                'required',
                'string',
                'max:50',
                Rule::unique('pedidos', 'numero_factura'),
            ],
            'proveedor_id' => [
                'required',
                'integer',
                'exists:proveedores,id',
            ],
            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'fecha_orden' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'fecha_esperada' => [
                'required',
                'date',
                'after_or_equal:fecha_orden',
            ],
            'fecha_entrega' => [
                'nullable',
                'date',
                'after_or_equal:fecha_orden',
            ],
            'estado' => [
                'required',
                Rule::in(['pendiente', 'confirmado', 'en_camino', 'parcial', 'completado', 'cancelado']),
            ],
            'contacto_proveedor' => [
                'nullable',
                'string',
                'max:255',
            ],
            'telefono_proveedor' => [
                'nullable',
                'string',
                'max:20',
            ],
            'subtotal' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'impuesto_porcentaje' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ],
            'monto_impuesto' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'total' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'observaciones' => [
                'nullable',
                'string',
            ],
            'terminos_pago' => [
                'nullable',
                'string',
            ],
            'condiciones_entrega' => [
                'nullable',
                'string',
            ],
            'detalles' => [
                'required',
                'array',
                'min:1',
            ],
            'detalles.*.producto_id' => [
                'required',
                'integer',
                'exists:productos,id',
            ],
            'detalles.*.cantidad' => [
                'required',
                'numeric',
                'min:1',
            ],
            'detalles.*.precio_unitario' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'detalles.*.aceite_id' => [
                'nullable',
                'integer',
                'exists:aceites,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'numero_factura.required' => 'El número de orden es requerido.',
            'numero_factura.unique' => 'Este número de orden ya existe en el sistema.',
            'proveedor_id.required' => 'Debes seleccionar un proveedor.',
            'proveedor_id.exists' => 'El proveedor seleccionado no existe.',
            'fecha_orden.required' => 'La fecha de orden es requerida.',
            'fecha_orden.before_or_equal' => 'La fecha de orden no puede ser en el futuro.',
            'fecha_esperada.required' => 'La fecha esperada de entrega es requerida.',
            'fecha_esperada.after_or_equal' => 'La fecha esperada debe ser igual o posterior a la fecha de orden.',
            'fecha_entrega.after_or_equal' => 'La fecha de entrega debe ser igual o posterior a la fecha de orden.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'impuesto_porcentaje.max' => 'El porcentaje de impuesto no puede exceder 100.',
            'total.min' => 'El total debe ser mayor a cero.',
            'detalles.required' => 'Debe agregar al menos un producto al pedido.',
            'detalles.min' => 'Debe agregar al menos un producto al pedido.',
            'detalles.*.producto_id.required' => 'Cada detalle debe tener un producto seleccionado.',
            'detalles.*.producto_id.exists' => 'El producto seleccionado no existe.',
            'detalles.*.cantidad.required' => 'La cantidad es requerida.',
            'detalles.*.cantidad.min' => 'La cantidad debe ser mayor a 0.',
            'detalles.*.precio_unitario.required' => 'El precio unitario es requerido.',
            'detalles.*.precio_unitario.min' => 'El precio debe ser mayor a 0.',
        ];
    }
}
