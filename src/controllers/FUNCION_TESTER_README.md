# 🎬 Probador Interactivo de FuncionController

## 📋 Descripción

Interfaz web profesional e interactiva para probar todos los métodos del `FuncionController` sin necesidad de hacer peticiones HTTP manuales o usar herramientas externas como Postman.

## 🚀 Archivos del Sistema

- **`funcion_tester.html`** - Interfaz web interactiva y moderna
- **`funcion_tester_api.php`** - API que procesa las peticiones y ejecuta los métodos
- **`funcion_controller.php`** - Controlador con todos los métodos disponibles

## 💻 Cómo Usar

### 1. Acceder a la Interfaz

Abre tu navegador y ve a:
```
http://localhost/cineplanet/src/controllers/funcion_tester.html
```

### 2. Seleccionar un Método

En el menú lateral izquierdo encontrarás todos los métodos disponibles organizados por tipo:
- **GET** (verde) - Métodos de lectura
- **POST** (amarillo) - Crear funciones
- **PUT** (azul) - Actualizar funciones
- **DELETE** (rojo) - Eliminar funciones

### 3. Completar los Parámetros

Cada método tiene su propio formulario con:
- **Descripción** del método
- **Campos requeridos** y opcionales
- **Hints** para guiarte

### 4. Ejecutar y Ver Resultados

- Haz clic en **"🚀 Ejecutar"**
- Los resultados aparecerán en formato JSON con:
  - Estado (Éxito/Error)
  - Respuesta completa del servidor
  - Resaltado de sintaxis

## 🔧 Métodos Disponibles

### 📖 Métodos de Lectura (GET)

| Método | Descripción | Parámetros |
|--------|-------------|------------|
| **getAll** | Obtener todas las funciones | Ninguno |
| **getById** | Obtener función por ID | `id` (requerido) |
| **getByPelicula** | Funciones de una película | `id_pelicula` (requerido) |
| **getBySede** | Funciones de una sede | `id_sede` (requerido) |
| **getByFecha** | Funciones de una fecha | `fecha` (requerido) |
| **getByFilters** | Filtrado avanzado | Múltiples filtros opcionales |
| **getAgrupadas** | Agrupadas por película | `id_ciudad` (opcional) |
| **getHorarios** | Horarios disponibles | `id_pelicula` (requerido), otros opcionales |
| **verificarDisponibilidad** | Verificar disponibilidad de sala | `id_sala`, `fecha`, `hora`, `duracion` |

### ✏️ Métodos de Escritura

| Método | Descripción | Parámetros |
|--------|-------------|------------|
| **create** | Crear nueva función | `fecha`, `hora`, `id_pelicula`, `id_sala` |
| **update** | Actualizar función | `id` + campos a actualizar |
| **delete** | Eliminar función | `id` (requerido) |

## 🎨 Características de la Interfaz

### ✨ Diseño Moderno
- Gradientes atractivos
- Animaciones suaves
- Diseño responsive para móviles
- Tarjetas con sombras y efectos hover

### 🎯 Funcionalidades
- **Validación visual** de campos requeridos
- **Resaltado de sintaxis JSON** en los resultados
- **Indicadores de estado** (éxito/error)
- **Hints y descripciones** para cada método
- **Botones de limpiar** para reiniciar formularios

### 📊 Visualización de Resultados
- Formato JSON legible
- Colores diferenciados por tipo de dato
- Scroll automático para resultados largos
- Estado visual del resultado (verde/rojo)

## 📝 Ejemplos de Uso

### Ejemplo 1: Obtener todas las funciones
1. Selecciona **"Obtener Todas"** en el menú lateral
2. Haz clic en **"🚀 Ejecutar"**
3. Verás todas las funciones con información completa

### Ejemplo 2: Crear una nueva función
1. Selecciona **"Crear Función"** en el menú lateral
2. Completa todos los campos:
   - Fecha: `2025-12-01`
   - Hora: `20:00`
   - ID Película: `1`
   - ID Sala: `1`
3. Haz clic en **"➕ Crear Función"**
4. Si es exitoso, recibirás el ID de la función creada

### Ejemplo 3: Filtrado avanzado
1. Selecciona **"Con Filtros"**
2. Completa los filtros que necesites:
   - ID Película: `1`
   - Fecha Desde: `2025-10-15`
   - Fecha Hasta: `2025-10-20`
3. Haz clic en **"🚀 Ejecutar"**
4. Obtendrás solo las funciones que cumplan los criterios

### Ejemplo 4: Verificar disponibilidad de sala
1. Selecciona **"Verificar Sala"**
2. Completa los datos:
   - ID Sala: `1`
   - Fecha: `2025-12-25`
   - Hora: `15:00`
   - Duración: `120` minutos
3. Haz clic en **"🚀 Ejecutar"**
4. Sabrás si la sala está disponible o hay conflicto

## 🐛 Solución de Problemas

### Error: "Acción no válida"
- Verifica que `funcion_tester_api.php` existe en el mismo directorio
- Asegúrate de que el servidor web está ejecutándose

### Error de conexión a la base de datos
- Verifica que MySQL/MariaDB está activo
- Comprueba las credenciales en `conexion.php`
- Asegúrate de que la base de datos `cineplanet` existe

### Los campos no se envían correctamente
- Verifica que completaste todos los campos requeridos (marcados con *)
- Revisa la consola del navegador (F12) para ver errores JavaScript

### El JSON no se muestra correctamente
- Actualiza la página (F5)
- Limpia la caché del navegador
- Verifica que PHP está procesando correctamente los archivos

## 🔒 Seguridad

**⚠️ Advertencia:** Esta herramienta es para desarrollo y pruebas. Para producción:
- Implementa autenticación y autorización
- Valida todos los inputs en el servidor
- Usa HTTPS
- Implementa rate limiting
- Registra todas las operaciones en logs

## 📱 Responsive Design

La interfaz funciona perfectamente en:
- 💻 **Desktop**: Layout de 2 columnas (menú + contenido)
- 📱 **Tablet/Mobile**: Layout de 1 columna adaptativo

## 🎯 Casos de Uso

### Para Desarrolladores
- Probar nuevas funcionalidades
- Debug de métodos del controlador
- Verificar respuestas de la API
- Validar reglas de negocio

### Para QA/Testers
- Pruebas funcionales
- Validación de datos
- Casos de prueba manuales
- Verificación de errores

### Para Demos
- Mostrar funcionalidad al cliente
- Presentaciones de avance
- Capacitación de usuarios

## 🚀 Próximas Mejoras

- [ ] Historial de peticiones
- [ ] Exportar resultados a JSON/CSV
- [ ] Guardar configuraciones favoritas
- [ ] Modo oscuro
- [ ] Pruebas automatizadas en batch
- [ ] Comparación de respuestas

## 📄 Licencia

Este código es parte del proyecto CinePlanet.

---

**Desarrollado con ❤️ para facilitar el desarrollo y testing del sistema CinePlanet**
