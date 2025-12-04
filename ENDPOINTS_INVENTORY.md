# Inventario Completo de Endpoints - TallerGonzalez API

## 📋 Resumen General

**Total de Endpoints:** 18
- **Públicos:** 3 (sin autenticación)
  - POST /login
  - GET /openapi.yaml
  - GET /openapi-debug
- **Protegidos:** 15 (requieren Bearer token)

---

## 🔐 Autenticación

### 1. **POST /api/login** ✅ PÚBLICO
- **Descripción:** Iniciar sesión y obtener token Bearer
- **Throttle:** 10 intentos por minuto
- **Rate Limit:** 5 intentos por 15 minutos por IP
- **Request:**
  ```json
  {
    "email": "usuario@example.com",
    "password": "tu_contraseña"
  }
  ```
- **Response (200):**
  ```json
  {
    "user": { "id": 1, "name": "Usuario", "email": "usuario@example.com", "roles": [...] },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "message": "Sesión iniciada correctamente"
  }
  ```

### 2. **GET /api/user** 🔒 AUTENTICADO
- **Descripción:** Obtener datos del usuario autenticado
- **Headers:** `Authorization: Bearer {token}`
- **Response (200):**
  ```json
  {
    "user": { "id": 1, "name": "Usuario", "email": "usuario@example.com", ... }
  }
  ```

### 3. **GET /api/check-auth** 🔒 AUTENTICADO
- **Descripción:** Verificar si el usuario está autenticado
- **Headers:** `Authorization: Bearer {token}`
- **Response (200):**
  ```json
  {
    "authenticated": true,
    "user": { "id": 1, "name": "Gabriel", ... }
  }
  ```

### 4. **POST /api/logout** 🔒 AUTENTICADO
- **Descripción:** Cerrar sesión actual
- **Headers:** `Authorization: Bearer {token}`
- **Response (200):**
  ```json
  { "message": "Sesión cerrada correctamente" }
  ```

### 5. **POST /api/refresh-token** 🔒 AUTENTICADO
- **Descripción:** Generar un nuevo token Bearer
- **Headers:** `Authorization: Bearer {token}`
- **Response (200):**
  ```json
  {
    "user": { ... },
    "token": "nuevo_token...",
    "message": "Token renovado"
  }
  ```

---

## 👥 Clientes

### 7. **GET /api/clientes** 🔒 AUTENTICADO
- **Descripción:** Listar clientes con filtros y paginación
- **Headers:** `Authorization: Bearer {token}`
- **Query Parameters:**
  - `search` - Búsqueda por nombre/razón social/email/teléfono/DUI/NIT
  - `tipo_cliente` - Filtrar por tipo de cliente
  - `activo` - Solo clientes activos (true/false)
  - `credito_activo` - Solo clientes con crédito activo (true/false)
  - `sort_field` - Campo para ordenar (default: `nombre`)
  - `sort_direction` - Orden: `asc` o `desc` (default: `asc`)
  - `per_page` - Clientes por página (default: 20)
- **Response (200):**
  ```json
  {
    "data": [
      {
        "id": 1,
        "codigo_cliente": "CLI-001",
        "nombre": "Juan",
        "apellido": "Pérez",
        "email": "juan@example.com",
        "telefono": "2234-5678",
        "tipo_cliente": "juridica",
        "razon_social": "Distribuidora ABC",
        "limite_credito": 5000,
        "dias_credito": 30,
        "activo": true,
        "credito_activo": true
      }
    ]
  }
  ```

### 8. **GET /api/clientes/{id}** 🔒 AUTENTICADO
- **Descripción:** Obtener detalles completos de un cliente
- **Headers:** `Authorization: Bearer {token}`
- **Parameters:**
  - `id` (path) - ID del cliente
- **Response (200):** Datos completos del cliente
- **Response (404):** Cliente no encontrado

### 9. **GET /api/clientes/buscar/{documento}** 🔒 AUTENTICADO
- **Descripción:** Buscar cliente por DUI o NIT
- **Headers:** `Authorization: Bearer {token}`
- **Parameters:**
  - `documento` (path) - Número de DUI o NIT
- **Response (200):** Cliente encontrado con todos sus datos
- **Response (404):** Cliente no encontrado
- **Nota:** Busca exactamente por DUI o NIT

---

## 📦 Productos

### 10. **GET /api/productos** 🔒 AUTENTICADO
- **Descripción:** Listar productos con filtros y paginación
- **Headers:** `Authorization: Bearer {token}`
- **Query Parameters:**
  - `search` - Búsqueda por nombre/código/descripción
  - `codigo` - Filtrar por código exacto
  - `tipo` - Filtrar por tipo: `aceite` o `normal`
  - `con_stock` - Solo productos con stock > 0 (true/false)
  - `sort_field` - Campo para ordenar (default: `nombre`)
  - `sort_direction` - Orden: `asc` o `desc` (default: `asc`)
  - `per_page` - Productos por página (default: 20)
- **Response (200):**
  ```json
  {
    "data": [
      {
        "id": 1,
        "codigo": "ACEITE-001",
        "nombre": "Aceite Motor 5W30",
        "precio_unitario": 45.50,
        "stock_actual": 100,
        "stock_minimo": 20,
        "marca": { ... },
        "categoria": { ... }
      }
    ]
  }
  ```

### 11. **GET /api/productos/buscar/{codigo}** 🔒 AUTENTICADO
- **Descripción:** Buscar producto por código exacto
- **Headers:** `Authorization: Bearer {token}`
- **Parameters:**
  - `codigo` (path) - Código del producto
- **Response (200):** Producto con todos los detalles
- **Response (404):** Producto no encontrado

### 12. **GET /api/productos/{id}** 🔒 AUTENTICADO
- **Descripción:** Obtener detalles completos de un producto
- **Headers:** `Authorization: Bearer {token}`
- **Parameters:**
  - `id` (path) - ID del producto
- **Response (200):** Datos completos del producto
- **Response (404):** Producto no encontrado

### 13. **GET /api/productos/tipo/{tipo}** 🔒 AUTENTICADO
- **Descripción:** Obtener productos filtrados por tipo
- **Headers:** `Authorization: Bearer {token}`
- **Parameters:**
  - `tipo` (path) - `aceite` o `normal`
- **Response (200):** Array de productos del tipo especificado

### 14. **GET /api/productos/stock/bajo** 🔒 AUTENTICADO
- **Descripción:** Listar productos con stock inferior al mínimo
- **Headers:** `Authorization: Bearer {token}`
- **Response (200):** Array de productos con stock bajo
- **Nota:** Ordenados por stock ascendente

---

## 💰 Facturas

### 15. **POST /api/facturas** 🔒 AUTENTICADO
- **Descripción:** Crear nueva factura (con bloqueo de concurrencia en stock)
- **Headers:** `Authorization: Bearer {token}`
- **Request:**
  ```json
  {
    "numero_factura": "FAC-20241204001",  // Opcional
    "cliente_id": 1,                       // Opcional (crea cliente si no existe)
    "cliente": "Juan Pérez",               // Nombre del cliente
    "fecha": "2024-12-04",                 // Opcional (default: hoy)
    "items": [
      {
        "producto_id": 1,
        "cantidad": 5,
        "precio_unitario": 100.00
      }
    ]
  }
  ```
- **Response (201):**
  ```json
  {
    "success": true,
    "data": { "id": 1, "numero_factura": "...", "total": 500.00, ... },
    "message": "Factura creada"
  }
  ```
- **Response (422):** Error de validación o stock insuficiente
- **Características:**
  - Bloquea fila del producto (`lockForUpdate`) para evitar condiciones de carrera
  - Decrementa stock automáticamente si `control_stock` está activo
  - Transacción atómica (rollback en caso de error)

### 16. **GET /api/facturas** 🔒 AUTENTICADO
- **Descripción:** Listar facturas paginadas con filtros
- **Headers:** `Authorization: Bearer {token}`
- **Query Parameters:**
  - `from` - Fecha inicial (YYYY-MM-DD)
  - `to` - Fecha final (YYYY-MM-DD)
  - `cliente_id` - Filtrar por cliente
  - `estado` - Filtrar por estado: `pendiente`, `pagada`, `cancelada`
  - `per_page` - Facturas por página (default: 15)
- **Response (200):**
  ```json
  {
    "data": [ { "id": 1, "numero_factura": "...", ... } ],
    "meta": { "total": 45, "per_page": 15, "current_page": 1 }
  }
  ```

### 17. **GET /api/facturas/{id}** 🔒 AUTENTICADO
- **Descripción:** Obtener detalles completos de una factura
- **Headers:** `Authorization: Bearer {token}`
- **Parameters:**
  - `id` (path) - ID de la factura
- **Response (200):** Factura con detalles de productos y creador
- **Response (404):** Factura no encontrada

### 18. **PUT /api/facturas/{id}** 🔒 AUTENTICADO
- **Descripción:** Actualizar estado de factura o marcarla como pagada
- **Headers:** `Authorization: Bearer {token}`
- **Parameters:**
  - `id` (path) - ID de la factura
- **Request Option 1 - Cambiar estado:**
  ```json
  { "estado": "cancelada" }
  ```
- **Request Option 2 - Marcar como pagada:**
  ```json
  { "pago": true }
  ```
- **Response (200):** Factura actualizada
- **Características:**
  - Si cambia a estado `cancelada`, restaura automáticamente el stock
  - Transacción atómica

---

## 📄 Documentación

### 19. **GET /api/openapi.yaml** ✅ PÚBLICO
- **Descripción:** Obtener especificación OpenAPI en formato YAML
- **Response (200):** Archivo YAML con documentación completa
- **Uso:** Se sirve en `/api/docs` para Swagger UI

---

## 🎯 Resumen de Seguridad

| Recurso | Autenticación | Rate Limit | Detalles |
|---------|---------------|-----------|----------|
| POST /login | ❌ Público | 10/min | 5 intentos fallidos/15min por IP |
| GET /productos | ❌ Público | ❌ No | Búsqueda pública |
| POST /facturas | ✅ Sanctum | ❌ No | Bloquea concurrencia en BD |
| PUT /facturas/{id} | ✅ Sanctum | ❌ No | Restaura stock si cancela |
| GET /user | ✅ Sanctum | ❌ No | Token Bearer requerido |
| POST /logout | ✅ Sanctum | ❌ No | Revoca token actual |

---

## 🚀 Credenciales de Prueba

Solicita credenciales de prueba al administrador del sistema.

---

## 📍 URLs Base

- **Desarrollo Local:** `http://localhost/api`
- **Swagger UI:** `http://localhost/api/docs`
- **OpenAPI Spec:** `http://localhost/api/openapi.yaml`

---

## ✅ Mejoras Implementadas

- ✅ **Concurrencia:** `DB::lockForUpdate()` en creación de facturas
- ✅ **Rate Limiting:** 5 intentos fallidos/15 min por IP
- ✅ **Throttling:** 10 requests/minuto en login
- ✅ **Transacciones Atómicas:** Rollback automático en errores
- ✅ **Stock Management:** Decremento/restauración automática
- ✅ **Swagger UI:** Documentación interactiva con login integrado
- ✅ **Respuestas Uniformes:** Helper `ApiResponse` para JSON consistente

---

## 🔍 Pruebas Disponibles

Ejecuta los tests con:
```bash
php artisan test
php artisan test --filter=Auth
php artisan test --filter=Factura
```

Tests incluidos:
- ✅ AuthTest.php - Flujo de login, refresh, logout
- ✅ FacturaApiTest.php - Crear, listar, cancelar facturas, restauración de stock
