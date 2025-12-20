<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class TestMailController extends Controller
{
    public function send()
    {
        try {
            Mail::raw('Este es un correo de prueba desde Taller Gonzalez.', function ($message) {
                $message->to('Gabriel503.alegria@gmail.com')
                        ->subject('Prueba de Correo Taller Gonzalez');
            });
            return 'Correo enviado correctamente (o encolado). Revise logs o bandeja de entrada.';
        } catch (\Exception $e) {
            return 'Error al enviar correo: ' . $e->getMessage();
        }
    }
}
