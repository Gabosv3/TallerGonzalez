<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu correo electrónico</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 40px 30px;
            color: #333;
        }
        .content h2 {
            color: #FFA500;
            margin-top: 0;
        }
        .content p {
            line-height: 1.6;
            color: #666;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            background-color: #FFA500;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            margin: 20px 0;
            cursor: pointer;
        }
        .button:hover {
            background-color: #FF8C00;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px 30px;
            text-align: center;
            color: #999;
            font-size: 12px;
            border-top: 1px solid #eee;
        }
        .info-box {
            background-color: #f0f8ff;
            padding: 15px;
            border-left: 4px solid #FFA500;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-box p {
            margin: 5px 0;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 Verifica tu correo electrónico</h1>
        </div>
        
        <div class="content">
            <h2>Hola,</h2>
            
            <p>
                Bienvenido a <strong>Taller González</strong>. Para completar tu registro y acceder a todas las funciones del sistema, 
                necesitas verificar tu dirección de correo electrónico.
            </p>
            
            <p>
                Has recibido este correo porque se creó una cuenta con esta dirección de correo en nuestro sistema.
            </p>
            
            <div class="info-box">
                <p><strong>📧 Correo electrónico:</strong> {{ $email }}</p>
            </div>
            
            <p>
                Por favor, haz clic en el botón de abajo para verificar tu correo electrónico:
            </p>
            
            <center>
                <a href="{{ $verificationUrl }}" class="button">
                    Verificar mi correo electronico
                </a>
            </center>
            
            <p style="margin-top: 30px; color: #999; font-size: 12px;">
                Si no solicitaste esta cuenta, puedes ignorar este correo.<br>
                Este enlace expira en 24 horas.
            </p>
        </div>
        
        <div class="footer">
            <p>
                © 2025 Taller González. Todos los derechos reservados.<br>
                Este es un correo automático, por favor no respondas a este mensaje.
            </p>
        </div>
    </div>
</body>
</html>
