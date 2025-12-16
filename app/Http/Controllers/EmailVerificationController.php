<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class EmailVerificationController extends Controller
{
    /**
     * Verificar correo electrónico usando ID y firma
     * URL firmada: /email/verify?id=1&signature=xxxx
     */
    public function verify(Request $request)
    {
        // Validar que la URL está firmada correctamente
        if (!URL::hasValidSignature($request)) {
            return redirect('/administrativo/login')
                ->with('error', 'El enlace de verificación no es válido o ha expirado.');
        }

        // Obtener el usuario por ID
        $userId = $request->query('id');
        $user = User::findOrFail($userId);

        // Si ya está verificado, informar al usuario
        if ($user->email_verified_at !== null) {
            return redirect('/administrativo/login')
                ->with('info', 'Tu correo ya ha sido verificado anteriormente.');
        }

        // Actualizar el campo email_verified_at con la fecha actual
        $user->update(['email_verified_at' => now()]);

        return redirect('/administrativo/login')
            ->with('success', 'Correo verificado correctamente. Ya puedes iniciar sesión.');
    }

    /**
     * Verificar correo electrónico usando ID simple (sin firma)
     * URL simple: /email/verify/{id}
     * 
     * Menos seguro pero más simple - usa binding de modelos
     */
    public function verifySimple($id)
    {
        // Obtener el usuario por ID explícitamente
        $user = User::findOrFail($id);

        // Si ya está verificado, informar al usuario
        if ($user->email_verified_at !== null) {
            return redirect('/administrativo/login')
                ->with('info', 'Tu correo ya ha sido verificado anteriormente.');
        }

        // Actualizar el campo email_verified_at con UPDATE directo
        $user->update(['email_verified_at' => now()]);

        return redirect('/administrativo/login')
            ->with('success', 'Correo verificado correctamente. Ya puedes iniciar sesión.');
    }

    /**
     * Verificar correo electrónico con hash (compatibilidad con enlaces antiguos)
     * URL antigua: /email/verify/{id}/{hash}
     */
    public function verifyLegacy($id, $hash)
    {
        // Obtener el usuario por ID
        $user = User::findOrFail($id);

        // Si ya está verificado, informar al usuario
        if ($user->email_verified_at !== null) {
            return redirect('/administrativo/login')
                ->with('info', 'Tu correo ya ha sido verificado anteriormente.');
        }

        // Actualizar el campo email_verified_at
        $user->update(['email_verified_at' => now()]);

        return redirect('/administrativo/login')
            ->with('success', 'Correo verificado correctamente. Ya puedes iniciar sesión.');
    }
}
