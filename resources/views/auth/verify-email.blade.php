<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo electrónico - Taller González</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%);
            padding: 50px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
        }
        .header p {
            font-size: 16px;
            opacity: 0.95;
        }
        .content {
            padding: 40px 35px;
            color: #333;
        }
        .content h2 {
            color: #FFA500;
            font-size: 24px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .content p {
            line-height: 1.8;
            color: #555;
            margin-bottom: 18px;
            font-size: 15px;
        }
        .verification-section {
            background-color: #f8f8f8;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 25px;
            margin: 30px 0;
            text-align: center;
        }
        .verification-section p {
            margin-bottom: 15px;
            color: #666;
        }
        .verification-link {
            display: inline-block;
            background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%);
            color: white;
            padding: 14px 40px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s;
            box-shadow: 0 2px 4px rgba(255, 165, 0, 0.3);
        }
        .verification-link:hover {
            transform: scale(1.02);
        }
        .email-info {
            background-color: #f0f8ff;
            border-left: 4px solid #FFA500;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .email-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }
        .security-note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
            font-size: 14px;
            color: #856404;
            line-height: 1.6;
        }
        .features {
            margin: 25px 0;
            padding: 20px;
            background-color: #fafafa;
            border-radius: 6px;
        }
        .features h3 {
            color: #FFA500;
            font-size: 16px;
            margin-bottom: 12px;
        }
        .features ul {
            list-style: none;
            font-size: 14px;
            color: #666;
            line-height: 2;
        }
        .features li:before {
            content: "✓ ";
            color: #FFA500;
            font-weight: bold;
            margin-right: 8px;
        }
        .footer {
            background-color: #f5f5f5;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #999;
            line-height: 1.8;
        }
        .footer a {
            color: #FFA500;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 25px 0;
        }
        .text-center { text-align: center; }
        .text-muted { color: #999; }
        .bold { font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔐 Bienvenido a Taller González</h1>
            <p>Verifica tu correo para completar tu registro</p>
        </div>
        
        <!-- Main Content -->
        <div class="content">
            <h2>¡Tu cuenta está casi lista!</h2>
            
            <p>
                Hola,
            </p>
            
            <p>
                Gracias por registrarte en <span class="bold">Taller González</span>. Hemos creado una cuenta para ti y estamos listos para que comiences a usar nuestro sistema.
            </p>
            
            <p>
                Para asegurar la validez de tu correo electrónico y activar tu cuenta completamente, necesitamos que verifiques tu dirección de email haciendo clic en el botón de abajo.
            </p>
            
            <!-- Email Info -->
            <div class="email-info">
                <p><strong>📧 Correo registrado:</strong></p>
                <p>{{ $email }}</p>
            </div>
            
            <!-- Verification Section -->
            <div class="verification-section">
                <p style="margin-bottom: 20px;">Haz clic en el botón para verificar tu correo:</p>
                <a href="{{ $verificationUrl }}" class="verification-link">
                    ✓ Verificar mi correo
                </a>
                <p style="margin-top: 15px; font-size: 12px; color: #999;">
                    Este enlace expira en 24 horas por razones de seguridad
                </p>
            </div>
            
            <!-- Features -->
            <div class="features">
                <h3>Acceso a funciones premium:</h3>
                <ul>
                    <li>Gestión completa de inventario</li>
                    <li>Reportes detallados de ventas</li>
                    <li>Control de pedidos a proveedores</li>
                    <li>Soporte prioritario</li>
                </ul>
            </div>
            
            <!-- Security Note -->
            <div class="security-note">
                <strong>🔒 Seguridad:</strong> Nunca compartimos tu información personal. Este es un correo automático de seguridad. Si no solicitaste esta cuenta, puedes ignorar este mensaje.
            </div>
            
            <p style="font-size: 13px; color: #999; line-height: 1.6;">
                Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                <code style="background: #f5f5f5; padding: 3px 6px; border-radius: 3px; word-break: break-all; font-size: 12px;">{{ $verificationUrl }}</code>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p>
                <strong>Taller González</strong><br>
                Sistema de Gestión Integral
            </p>
            <div class="divider" style="margin: 15px 0;"></div>
            <p>
                Este es un correo automático de transacción. Por favor no respondas a este mensaje.<br>
                Si tienes problemas, contacta con <a href="mailto:administracion@motorsgonzalez.com">administracion@motorsgonzalez.com</a><br>
                © 2025 Taller González. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>
