# Documentación API de Autenticación

## Endpoints de Autenticación

### 1. Login (Público)
**POST** `/api/login`

**Validaciones:**
- Email: requerido, válido, máximo 255 caracteres, debe existir en BD
- Contraseña: requerida, mínimo 8 caracteres, máximo 255 caracteres

**Rate Limiting:**
- Máximo 5 intentos fallidos
- Bloqueo de 15 minutos después de exceder intentos

**Respuesta exitosa (200):**
```json
{
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "roles": ["cliente"]
  },
  "token": "token_de_acceso",
  "message": "Sesión iniciada correctamente"
}
```

**Errores posibles:**
- 422: Validación fallida
- 429: Demasiados intentos
- 403: Email no verificado, cuenta desactivada o sin permisos

---

### 2. Logout (Protegido)
**POST** `/api/logout`

**Headers requeridos:**
```
Authorization: Bearer {token}
```

**Respuesta (200):**
```json
{
  "message": "Sesión cerrada correctamente"
}
```

---

### 3. Obtener Usuario Actual (Protegido)
**GET** `/api/user`

**Headers requeridos:**
```
Authorization: Bearer {token}
```

**Respuesta (200):**
```json
{
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "roles": ["cliente"]
  }
}
```

---

### 4. Verificar Autenticación (Protegido)
**GET** `/api/check-auth`

**Headers requeridos:**
```
Authorization: Bearer {token}
```

**Respuesta (200):**
```json
{
  "authenticated": true,
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "roles": ["cliente"]
  }
}
```

---

### 5. Renovar Token (Protegido)
**POST** `/api/refresh-token`

**Headers requeridos:**
```
Authorization: Bearer {token}
```

**Validaciones:**
- Usuario debe estar activo y tener roles válidos

**Respuesta (200):**
```json
{
  "user": {
    "id": 1,
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "roles": ["cliente"]
  },
  "token": "nuevo_token",
  "message": "Token renovado"
}
```

**Errores:**
- 403: Acceso denegado

---

### 6. Cambiar Contraseña (Protegido)
**POST** `/api/change-password`

**Headers requeridos:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Body:**
```json
{
  "current_password": "password_actual",
  "password": "nueva_password",
  "password_confirmation": "nueva_password"
}
```

**Validaciones:**
- current_password: requerida, mínimo 8 caracteres
- password: requerida, mínimo 8 caracteres, debe coincidir con confirmation, diferente a current_password
- password_confirmation: requerida, mínimo 8 caracteres

**Respuesta (200):**
```json
{
  "message": "Contraseña actualizada correctamente"
}
```

**Errores:**
- 422: Validación fallida
- 401: Contraseña actual incorrecta

---

## Validaciones Implementadas

### Login
✅ Rate limiting contra fuerza bruta (5 intentos, 15 minutos de bloqueo)
✅ Validación de credenciales
✅ Verificación de email verificado
✅ Validación de usuario activo (no eliminado)
✅ Validación de roles activos
✅ Longitud mínima de contraseña (8 caracteres)

### Cambio de Contraseña
✅ Verificación de contraseña actual
✅ Confirmación de nueva contraseña
✅ Contraseña diferente a la anterior
✅ Longitud mínima (8 caracteres)

### Token
✅ Creación de token Sanctum
✅ Renovación de token
✅ Eliminación de token al logout

---

## Códigos de Estado HTTP

| Código | Significado |
|--------|-------------|
| 200 | Éxito |
| 401 | No autenticado |
| 403 | Acceso denegado |
| 422 | Validación fallida |
| 429 | Rate limit excedido |
| 500 | Error del servidor |

---

## Seguridad

- ✅ Passwords hasheadas con bcrypt
- ✅ Rate limiting en login
- ✅ Validación de email verificado
- ✅ Verificación de roles y permisos
- ✅ Tokens seguros con Sanctum
- ✅ Sanitización de entradas
- ✅ Protección contra fuerza bruta
