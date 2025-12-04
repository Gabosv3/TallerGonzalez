<?php

namespace App\Exceptions;

use Exception;

class PedidoNoEliminableException extends Exception
{
    public function __construct(string $message = 'No se puede eliminar un pedido que está completado o cancelado.', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
