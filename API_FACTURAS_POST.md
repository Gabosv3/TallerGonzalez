# Documentación - POST /api/facturas

## Endpoint
```
POST /api/facturas
```

## Autenticación
**Requerida**: Bearer Token (Sanctum)
```
Authorization: Bearer {tu_token}
```

---

## Request Body

El cuerpo debe ser JSON con la siguiente estructura:

### Estructura Obligatoria

```json
{
  "cliente": "string (requerido)",
  "items": [
    {
      "producto_id": integer (requerido),
      "cantidad": number (requerido, mínimo 1),
      "precio_unitario": number (requerido, mínimo 0)
    }
  ]
}
```

### Campos Opcionales

```json
{
  "numero_factura": "string (único, opcional - se genera automáticamente)",
  "cliente_id": "integer (opcional - si no viene, busca por nombre o crea cliente temporal)",
  "fecha": "YYYY-MM-DD (opcional - por defecto es la fecha actual)",
  "es_clientes_varios": "boolean (opcional - solo informativo)"
}
```

---

## Ejemplos de Uso

### ✅ Ejemplo 1: Básico (Recomendado para CLIENTES VARIOS)

```json
{
  "cliente": "CLIENTES VARIOS",
  "items": [
    {
      "producto_id": 1,
      "cantidad": 1,
      "precio_unitario": 10
    }
  ]
}
```

**Resultado**: 
- ✅ Se crea factura con número auto-generado
- ✅ Cliente: "CLIENTES VARIOS" (temporal)
- ✅ Fecha: Hoy
- ✅ Stock se decrementa automáticamente

---

### ✅ Ejemplo 2: Con cliente conocido (cliente_id)

```json
{
  "cliente_id": 5,
  "items": [
    {
      "producto_id": 1,
      "cantidad": 2,
      "precio_unitario": 15.50
    },
    {
      "producto_id": 3,
      "cantidad": 1,
      "precio_unitario": 25.00
    }
  ]
}
```

**Resultado**:
- ✅ Usa cliente_id = 5 existente
- ✅ 2 items en la factura
- ✅ Total = (2 × 15.50) + (1 × 25.00) = 56.00

---

### ✅ Ejemplo 3: Con número de factura personalizado

```json
{
  "numero_factura": "DTE-01-S001P001-000000000000005",
  "cliente": "Juan Pérez",
  "fecha": "2025-12-16",
  "items": [
    {
      "producto_id": 1,
      "cantidad": 1,
      "precio_unitario": 10
    }
  ]
}
```

**Resultado**:
- ✅ Factura con número específico
- ✅ Si "Juan Pérez" existe, usa su ID
- ✅ Si no existe, crea cliente temporal
- ✅ Fecha exacta: 2025-12-16

---

### ✅ Ejemplo 4: Múltiples items

```json
{
  "cliente": "CLIENTES VARIOS",
  "items": [
    {
      "producto_id": 1,
      "cantidad": 5,
      "precio_unitario": 10.00
    },
    {
      "producto_id": 2,
      "cantidad": 3,
      "precio_unitario": 20.50
    },
    {
      "producto_id": 5,
      "cantidad": 1,
      "precio_unitario": 100.00
    }
  ]
}
```

**Resultado**:
- ✅ Total = (5 × 10) + (3 × 20.50) + (1 × 100) = 211.50

---

## Validaciones

| Campo | Validación | Error si falla |
|-------|-----------|-----------------|
| `cliente` | string max:255, requerido si no hay cliente_id | "El campo cliente es obligatorio cuando no hay cliente_id" |
| `cliente_id` | debe existir en tabla clientes | "El cliente_id seleccionado no existe" |
| `items` | array mínimo 1 | "Se requiere al menos un item en la factura" |
| `items[].producto_id` | debe existir en tabla productos | "El producto especificado no existe" |
| `items[].cantidad` | numérico, mínimo 1 | "La cantidad debe ser al menos 1" |
| `items[].precio_unitario` | numérico, mínimo 0 | "El precio debe ser mayor a 0" |
| `numero_factura` | debe ser único | "El número de factura ya existe" |
| `fecha` | formato YYYY-MM-DD | "La fecha debe ser una fecha válida" |

---

## Errores Comunes

### ❌ Error 1: Sin autenticación
```
401 Unauthorized
```
**Solución**: Incluir header `Authorization: Bearer {token}`

---

### ❌ Error 2: Producto no existe
```json
{
  "success": false,
  "message": "No query results for model [App\\Models\\Producto] with value [999]"
}
```
**Solución**: Verificar que `producto_id` exista con `GET /api/productos`

---

### ❌ Error 3: Stock insuficiente
```json
{
  "success": false,
  "message": "Stock insuficiente para producto ID 5"
}
```
**Solución**: Reducir cantidad o comprar más inventario

---

### ❌ Error 4: Número de factura duplicado
```json
{
  "message": "The numero_factura has already been taken."
}
```
**Solución**: Usar número único o dejar vacío para auto-generar

---

## Respuesta Exitosa (201)

```json
{
  "success": true,
  "data": {
    "id": 42,
    "numero_factura": "FAC-20251217143022",
    "cliente": "CLIENTES VARIOS",
    "cliente_id": 150,
    "fecha": "2025-12-17",
    "total": 10.00,
    "estado": "pendiente",
    "created_by": 1,
    "created_at": "2025-12-17T14:30:22.000000Z",
    "updated_at": "2025-12-17T14:30:22.000000Z",
    "detalles": [
      {
        "id": 1,
        "factura_id": 42,
        "producto_id": 1,
        "cantidad": 1,
        "precio_unitario": 10,
        "subtotal": 10.00,
        "producto": {
          "id": 1,
          "codigo": "PROD-001",
          "nombre": "Producto Ejemplo",
          "precio_venta": 10.00,
          "stock_actual": 99
        }
      }
    ]
  },
  "message": "Factura creada"
}
```

---

## Comportamiento Automático

### 1. Número de Factura (si no se envía)
```
FAC-{YYYYMMDDHHmmss}
Ejemplo: FAC-20251217143022
```

### 2. Cliente Temporal (si `cliente` no existe y no hay `cliente_id`)
- **Nombre**: El valor de `cliente`
- **Apellido**: "Temporal"
- **Email**: temporal-{random}@no-email.local
- **Teléfono**: 0000-0000
- **DUI**: TEMPORAL-{random}
- **Tipo**: consumidor_final
- **Activo**: true

### 3. Actualización de Stock
```
Si el producto tiene control_stock = true:
  stock_actual -= cantidad
```

### 4. Cálculo de Total
```
Total = Suma de (cantidad × precio_unitario) para todos los items
```

---

## Endpoints Relacionados

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/facturas` | Listar facturas (con filtros) |
| GET | `/api/facturas/{id}` | Obtener factura específica |
| PUT | `/api/facturas/{id}` | Actualizar estado/pago |
| GET | `/api/productos` | Listar productos disponibles |
| GET | `/api/clientes` | Listar clientes conocidos |

---

## cURL de Ejemplo

```bash
curl -X POST http://localhost/api/facturas \
  -H "Authorization: Bearer tu_token_aqui" \
  -H "Content-Type: application/json" \
  -d '{
    "cliente": "CLIENTES VARIOS",
    "items": [
      {
        "producto_id": 1,
        "cantidad": 1,
        "precio_unitario": 10
      }
    ]
  }'
```

---

## JavaScript (Fetch) de Ejemplo

```javascript
const response = await fetch('/api/facturas', {
  method: 'POST',
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    cliente: 'CLIENTES VARIOS',
    items: [
      {
        producto_id: 1,
        cantidad: 1,
        precio_unitario: 10
      }
    ]
  })
});

const result = await response.json();
console.log(result);
```

---

## Notas Importantes

1. **La autenticación es obligatoria** - Necesitas hacer login primero con `/api/login`
2. **El total se calcula automáticamente** - No se envía en el request
3. **El stock se resta automáticamente** - Solo si `control_stock = true`
4. **Los clientes temporales** se crean solo si es necesario
5. **La factura comienza con estado "pendiente"**
6. **IVA no se aplica automáticamente** - Se usa el precio unitario exacto enviado

