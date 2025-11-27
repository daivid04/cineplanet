# API de Funciones - Documentación

## Descripción
API REST para gestionar las funciones (horarios de películas) en el sistema Cineplanet.

---

## Endpoints

### 1. **Obtener todas las funciones**
**GET** `/api/funcion_api.php`

**Respuesta exitosa:**
```json
{
  "success": true,
  "data": [
    {
      "id_funcion": 1,
      "fecha": "2025-10-17",
      "hora": "18:00:00",
      "id_pelicula": 1,
      "id_sala": 1,
      "pelicula_nombre": "Dune: Parte Dos",
      "pelicula_duracion": 155,
      "pelicula_imagen": "https://example.com/images/dune2.jpg",
      "numero_sala": 1,
      "sede_nombre": "Cineplanet Real Plaza Tacna",
      "id_sede": 1,
      "ciudad_nombre": "Tacna"
    }
  ],
  "total": 10
}
```

---

### 2. **Obtener función por ID**
**GET** `/api/funcion_api.php?id={id}`

**Parámetros:**
- `id` (número): ID de la función

**Ejemplo:** `/api/funcion_api.php?id=1`

---

### 3. **Obtener funciones por película**
**GET** `/api/funcion_api.php?accion=por_pelicula&id_pelicula={id}`

**Parámetros:**
- `id_pelicula` (número): ID de la película

**Ejemplo:** `/api/funcion_api.php?accion=por_pelicula&id_pelicula=1`

---

### 4. **Obtener funciones por sede**
**GET** `/api/funcion_api.php?accion=por_sede&id_sede={id}`

**Parámetros:**
- `id_sede` (número): ID de la sede

**Ejemplo:** `/api/funcion_api.php?accion=por_sede&id_sede=3`

---

### 5. **Obtener funciones por fecha**
**GET** `/api/funcion_api.php?accion=por_fecha&fecha={fecha}`

**Parámetros:**
- `fecha` (string): Fecha en formato YYYY-MM-DD

**Ejemplo:** `/api/funcion_api.php?accion=por_fecha&fecha=2025-10-17`

---

### 6. **Obtener funciones con filtros combinados**
**GET** `/api/funcion_api.php?accion=con_filtros&[parámetros]`

**Parámetros opcionales:**
- `id_pelicula` (número): Filtrar por película
- `id_sede` (número): Filtrar por sede
- `id_ciudad` (número): Filtrar por ciudad
- `fecha` (string): Filtrar por fecha específica (YYYY-MM-DD)
- `fecha_desde` (string): Filtrar desde fecha (YYYY-MM-DD)
- `fecha_hasta` (string): Filtrar hasta fecha (YYYY-MM-DD)

**Ejemplo:** `/api/funcion_api.php?accion=con_filtros&id_pelicula=1&id_ciudad=1&fecha_desde=2025-10-17`

---

### 7. **Obtener funciones agrupadas por película**
**GET** `/api/funcion_api.php?accion=agrupadas_por_pelicula&[parámetros]`

**Parámetros opcionales:**
- `id_ciudad` (número): Filtrar por ciudad
- `fecha` (string): Filtrar por fecha
- `fecha_desde` (string): Desde fecha
- `fecha_hasta` (string): Hasta fecha

**Ejemplo:** `/api/funcion_api.php?accion=agrupadas_por_pelicula&id_ciudad=1`

**Respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "id_pelicula": 1,
      "nombre": "Dune: Parte Dos",
      "duracion": 155,
      "url_imagen": "https://...",
      "funciones": [
        {
          "id_funcion": 1,
          "fecha": "2025-10-17",
          "hora": "18:00:00",
          "sala": 1,
          "sede": "Cineplanet Jockey Plaza",
          "id_sede": 3,
          "ciudad": "Lima"
        }
      ]
    }
  ],
  "total_peliculas": 5,
  "total_funciones": 15
}
```

---

### 8. **Obtener horarios disponibles para una película**
**GET** `/api/funcion_api.php?accion=horarios_disponibles&id_pelicula={id}&[parámetros]`

**Parámetros:**
- `id_pelicula` (número, requerido): ID de la película
- `fecha` (string, opcional): Fecha específica
- `id_ciudad` (número, opcional): Filtrar por ciudad

**Ejemplo:** `/api/funcion_api.php?accion=horarios_disponibles&id_pelicula=1&id_ciudad=1`

**Respuesta:**
```json
{
  "success": true,
  "data": [
    {
      "fecha": "2025-10-17",
      "sedes": [
        {
          "id_sede": 3,
          "nombre_sede": "Cineplanet Jockey Plaza",
          "ciudad": "Lima",
          "horarios": [
            {
              "id_funcion": 1,
              "hora": "18:00:00",
              "sala": 1
            },
            {
              "id_funcion": 2,
              "hora": "21:00:00",
              "sala": 1
            }
          ]
        }
      ]
    }
  ],
  "total_fechas": 3
}
```

---

### 9. **Verificar disponibilidad de sala**
**GET** `/api/funcion_api.php?accion=verificar_disponibilidad&id_sala={id}&fecha={fecha}&hora={hora}&duracion={minutos}`

**Parámetros:**
- `id_sala` (número): ID de la sala
- `fecha` (string): Fecha en formato YYYY-MM-DD
- `hora` (string): Hora en formato HH:MM o HH:MM:SS
- `duracion` (número): Duración de la película en minutos
- `id_funcion_excluir` (número, opcional): ID de función a excluir (útil al actualizar)

**Ejemplo:** `/api/funcion_api.php?accion=verificar_disponibilidad&id_sala=1&fecha=2025-10-17&hora=20:00&duracion=155`

**Respuesta:**
```json
{
  "success": true,
  "disponible": true,
  "message": "La sala está disponible para el horario solicitado"
}
```

---

### 10. **Crear función**
**POST** `/api/funcion_api.php`

**Body (JSON):**
```json
{
  "fecha": "2025-10-25",
  "hora": "19:30:00",
  "id_pelicula": 1,
  "id_sala": 5
}
```

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Función creada exitosamente",
  "id_funcion": 11
}
```

---

### 11. **Actualizar función**
**PUT** `/api/funcion_api.php?id={id}`

**Body (JSON):**
```json
{
  "fecha": "2025-10-26",
  "hora": "20:00:00"
}
```

**Nota:** Solo incluye los campos que deseas actualizar.

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Función actualizada exitosamente"
}
```

---

### 12. **Eliminar función**
**DELETE** `/api/funcion_api.php?id={id}`

**Parámetros:**
- `id` (número): ID de la función a eliminar

**Respuesta exitosa:**
```json
{
  "success": true,
  "message": "Función eliminada exitosamente"
}
```

---

## Códigos de Estado HTTP

- `200 OK`: Operación exitosa
- `201 Created`: Recurso creado exitosamente
- `400 Bad Request`: Error en la solicitud o datos inválidos
- `404 Not Found`: Recurso no encontrado
- `405 Method Not Allowed`: Método HTTP no permitido

---

## Validaciones

### Fecha
- Formato: `YYYY-MM-DD`
- Ejemplo: `2025-10-17`

### Hora
- Formato: `HH:MM` o `HH:MM:SS`
- Ejemplo: `18:00` o `18:00:00`

### IDs
- Deben ser números enteros positivos

---

## Ejemplos de Uso

### JavaScript (Fetch API)

```javascript
// Obtener todas las funciones
fetch('http://tu-dominio.com/api/funcion_api.php')
  .then(response => response.json())
  .then(data => console.log(data));

// Obtener horarios disponibles para una película
fetch('http://tu-dominio.com/api/funcion_api.php?accion=horarios_disponibles&id_pelicula=1&id_ciudad=1')
  .then(response => response.json())
  .then(data => console.log(data));

// Crear una nueva función
fetch('http://tu-dominio.com/api/funcion_api.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    fecha: '2025-10-25',
    hora: '19:30:00',
    id_pelicula: 1,
    id_sala: 5
  })
})
  .then(response => response.json())
  .then(data => console.log(data));

// Actualizar función
fetch('http://tu-dominio.com/api/funcion_api.php?id=1', {
  method: 'PUT',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    hora: '20:00:00'
  })
})
  .then(response => response.json())
  .then(data => console.log(data));

// Eliminar función
fetch('http://tu-dominio.com/api/funcion_api.php?id=1', {
  method: 'DELETE'
})
  .then(response => response.json())
  .then(data => console.log(data));
```

---

## Casos de Uso Comunes

### 1. Mostrar cartelera de películas con horarios
```javascript
// Obtener funciones agrupadas por película para Lima
const url = '/api/funcion_api.php?accion=agrupadas_por_pelicula&id_ciudad=1&fecha_desde=2025-10-17';
```

### 2. Mostrar horarios de una película específica
```javascript
// Obtener horarios disponibles para la película con ID 1
const url = '/api/funcion_api.php?accion=horarios_disponibles&id_pelicula=1';
```

### 3. Verificar si puedo agregar una nueva función
```javascript
// Verificar si la sala 1 está disponible
const url = '/api/funcion_api.php?accion=verificar_disponibilidad&id_sala=1&fecha=2025-10-17&hora=20:00&duracion=155';
```

---

## Notas Importantes

1. **Verificación de disponibilidad**: Siempre verifica la disponibilidad de la sala antes de crear una nueva función para evitar solapamientos.

2. **Formato de hora**: Acepta tanto `HH:MM` como `HH:MM:SS`. El sistema agregará los segundos automáticamente si no se proporcionan.

3. **Filtros combinados**: Puedes combinar múltiples filtros para búsquedas más específicas.

4. **Transacciones**: Las operaciones de creación, actualización y eliminación están protegidas con transacciones para mantener la integridad de los datos.

5. **Validaciones**: Todos los endpoints validan los datos de entrada antes de procesarlos.
