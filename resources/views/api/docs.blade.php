<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>API Docs - TallerGonzalez</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@4/swagger-ui.css" />
    <style>
      body { margin: 0; padding: 0; font-family: sans-serif; }
      #login-bar { background: #f5f5f5; border-bottom: 1px solid #ddd; padding: 12px 20px; display: flex; gap: 8px; align-items: center; }
      #login-bar input { padding: 6px 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; }
      #login-bar button { padding: 6px 12px; background: #0b5fff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; }
      #login-bar button:hover { background: #0a47d9; }
      #login-bar button:disabled { background: #ccc; cursor: not-allowed; }
      #auth-msg { font-size: 13px; color: #444; margin-left: 8px; }
    </style>
  </head>
  <body>
    <div id="login-bar">
      <input id="auth-email" placeholder="Email" />
      <input id="auth-password" type="password" placeholder="Password" />
      <button id="auth-btn">Login & Authorize</button>
      <span id="auth-msg"></span>
    </div>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@4/swagger-ui-bundle.js"></script>
    <script>
      window.onload = function() {
        const ui = SwaggerUIBundle({
          url: '/api/openapi.yaml',
          dom_id: '#swagger-ui',
          presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIBundle.SwaggerUIStandalonePreset
          ],
          layout: 'BaseLayout',
          deepLinking: true
        });
        window.ui = ui;

        // Login & Authorize
        document.getElementById('auth-btn').addEventListener('click', async function() {
          const email = document.getElementById('auth-email').value.trim();
          const password = document.getElementById('auth-password').value.trim();
          const msgEl = document.getElementById('auth-msg');
          const btn = this;

          if (!email || !password) {
            msgEl.textContent = 'Completa email y password';
            msgEl.style.color = '#d32f2f';
            return;
          }

          btn.disabled = true;
          msgEl.textContent = 'Autenticando...';
          msgEl.style.color = '#1976d2';

          try {
            // Usar URL absoluta para evitar problemas de ruta relativa
            const loginUrl = window.location.origin + '/api/login';
            const res = await fetch(loginUrl, {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ email, password })
            });

            const data = await res.json();
            if (!res.ok) {
              msgEl.textContent = data.message || 'Error al autenticar';
              msgEl.style.color = '#d32f2f';
              btn.disabled = false;
              return;
            }

            const token = data.token;
            if (!token) {
              msgEl.textContent = 'No se recibió token';
              msgEl.style.color = '#d32f2f';
              btn.disabled = false;
              return;
            }

            // Swagger UI authorize
            if (window.ui && window.ui.preauthorizeApiKey) {
              window.ui.preauthorizeApiKey('bearerAuth', token);
            }

            msgEl.textContent = '✓ Autenticado';
            msgEl.style.color = '#388e3c';
            btn.disabled = false;
          } catch (err) {
            console.error(err);
            msgEl.textContent = 'Error de red';
            msgEl.style.color = '#d32f2f';
            btn.disabled = false;
          }
        });
      };
    </script>
  </body>
</html>
