<?php

use Illuminate\Support\Facades\Mail;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    echo "Intentando enviar correo...\n";
    Mail::raw('Prueba de correo desde script', function ($message) {
        $message->to('jonagozalez23@gmail.com')
                ->subject('Prueba Script');
    });
    echo "Correo enviado (o encolado).\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
