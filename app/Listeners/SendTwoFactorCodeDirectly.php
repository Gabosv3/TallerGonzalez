<?php

namespace App\Listeners;

use Backstage\TwoFactorAuth\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;

class SendTwoFactorCodeDirectly
{
    /**
     * Handle the event.
     */
    public function handle(TwoFactorAuthenticationChallenged|TwoFactorAuthenticationEnabled $event): void
    {
        Log::info('╔════════════════════════════════════════╗');
        Log::info('║ SendTwoFactorCodeDirectly::handle()    ║');
        Log::info('╚════════════════════════════════════════╝');

        try {
            /** @var mixed $user */
            $user = $event->user;

            Log::info("Usuario: {$user->email}");
            Log::info("Tipo de evento: " . class_basename($event));

            // Verificar que el usuario tenga two_factor_secret
            if (!$user->two_factor_secret) {
                Log::warning("⚠️ Usuario no tiene two_factor_secret configurado");
                return;
            }

            Log::info("✓ Usuario tiene two_factor_secret");

            // Generar el código OTP
            $otp = \Backstage\TwoFactorAuth\Actions\GenerateOTP::for(
                decrypt($user->two_factor_secret)
            );

            Log::info("✓ Código OTP generado: {$otp}");

            // Crear y enviar el correo
            $mailable = new TwoFactorCodeMail($otp);
            
            Mail::to($user->email)->send($mailable);

            Log::info("✅ Correo 2FA enviado EXITOSAMENTE a: {$user->email}");

        } catch (\Exception $e) {
            Log::error("❌ ERROR enviando correo 2FA: " . $e->getMessage());
            Log::error("Archivo: " . $e->getFile() . ":" . $e->getLine());
            Log::error("Stack: " . $e->getTraceAsString());
        }

        Log::info('╔════════════════════════════════════════╗');
        Log::info('║        Proceso Completado ✓            ║');
        Log::info('╚════════════════════════════════════════╝');
    }
}
