<x-mail::message>
# Restablecer Contraseña

Has recibido este correo porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta.

<x-mail::button :url="$url">
Restablecer Contraseña
</x-mail::button>

Este enlace de restablecimiento de contraseña caducará en 60 minutos.

Si no has solicitado un restablecimiento de contraseña, no es necesario realizar ninguna otra acción.

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
