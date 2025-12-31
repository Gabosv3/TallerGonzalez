<x-mail::message>
# 🔐 Restablecer Contraseña

¡Hola!

Has recibido este correo porque se ha solicitado un restablecimiento de contraseña para tu cuenta en **{{ config('app.name') }}**.

<x-mail::button :url="$url">
Restablecer Contraseña
</x-mail::button>

**Información importante:**
- Este enlace es **válido solo por 60 minutos**
- Si no solicitaste el restablecimiento, **ignora este correo**
- No compartas este enlace con nadie

Si tienes problemas al hacer clic en el botón, puedes copiar y pegar esta dirección en tu navegador:
{{ $url }}

---

Atentamente,<br>
**{{ config('app.name') }}**<br>
Sistema de Administración
</x-mail::message>
