# 🎬 Probador de Películas - CinePlanet

Sistema de pruebas CRUD completo para la gestión de películas en CinePlanet.

## 📁 Archivos del Sistema

- **`pelicula_tester.html`** - Interfaz web interactiva y moderna
- **`pelicula_tester_api.php`** - API que procesa las peticiones y ejecuta los métodos
- **`pelicula_controller.php`** - Controlador con todos los métodos disponibles
- **`pelicula_model.php`** - Modelo con las operaciones de base de datos

## 🚀 Cómo Usar

### 1. Acceder a la Interfaz
Abre el archivo `pelicula_tester.html` en tu navegador o accede a través del servidor:
```
http://localhost/cineplanet/src/controllers/pelicula_tester.html
```

### 2. Operaciones Disponibles

#### 📖 Ver Todas las Películas
- Obtiene todas las películas registradas en el sistema
- Incluye información de idiomas y formatos

#### 🔍 Buscar por ID
- Busca una película específica por su ID
- Retorna toda la información de la película

#### ✅ Ver Películas Activas
- Muestra solo las películas con estado activo (estado = 1)
- Útil para ver el catálogo actual

#### 🔎 Buscar por Nombre
- Búsqueda parcial por nombre de película
- Ejemplo: "Dune" encontrará "Dune: Parte Dos"

#### ➕ Crear Película
Campos requeridos:
- **Nombre**: Título de la película
- **Duración**: En minutos
- **URL de Imagen**: Link a la imagen/poster
- **Sinopsis**: Descripción de la película
- **Estado**: 1 (Activa) o 0 (Inactiva)

#### ✏️ Actualizar Película
- Actualiza campos específicos de una película existente
- Solo completa los campos que deseas modificar
- Requiere el ID de la película

#### 🔄 Cambiar Estado
- Activa o desactiva una película rápidamente
- Útil para gestionar el catálogo sin eliminar datos

#### 🗑️ Eliminar Película
- Elimina permanentemente una película
- ⚠️ **Advertencia**: Esta acción no se puede deshacer

## 🔧 API Endpoints

### Formato de Petición
```
POST pelicula_tester_api.php?action={operacion}
Content-Type: application/json
```

### Ejemplos de Uso

#### Obtener todas las películas
```javascript
fetch('pelicula_tester_api.php?action=getAll', {
    method: 'POST'
})
```

#### Buscar por ID
```javascript
fetch('pelicula_tester_api.php?action=getById', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id: 1 })
})
```

#### Crear película
```javascript
fetch('pelicula_tester_api.php?action=create', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        data: {
            nombre: "Avatar 3",
            duracion: 180,
            url_imagen: "https://example.com/avatar3.jpg",
            sinopsis: "La saga continúa...",
            estado: 1
        }
    })
})
```

#### Actualizar película
```javascript
fetch('pelicula_tester_api.php?action=update', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        id: 1,
        data: {
            nombre: "Nuevo Nombre",
            duracion: 150
        }
    })
})
```

#### Cambiar estado
```javascript
fetch('pelicula_tester_api.php?action=cambiarEstado', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        id: 1,
        estado: 0  // 0 = Inactiva, 1 = Activa
    })
})
```

## 📊 Formato de Respuesta

### Respuesta Exitosa
```json
{
    "success": true,
    "data": [...],
    "total": 10
}
```

### Respuesta de Error
```json
{
    "success": false,
    "message": "Descripción del error"
}
```

## 🎨 Características de la Interfaz

- ✨ Diseño moderno y responsivo
- 🎯 Interfaz intuitiva con pestañas
- 📱 Compatible con dispositivos móviles
- 🌈 Resaltado de sintaxis JSON
- ⚡ Respuestas en tiempo real
- 🔔 Mensajes de éxito/error claros

## 💡 Notas Importantes

1. **Estado de Películas**: El campo `estado` permite activar/desactivar películas sin eliminarlas
2. **Idiomas y Formatos**: Se muestran como texto concatenado en las consultas
3. **Validaciones**: La API valida todos los campos requeridos antes de procesar
4. **Seguridad**: Usa prepared statements para prevenir SQL injection

## 🐛 Solución de Problemas

### Error de Conexión
- Verifica que el servidor esté corriendo
- Revisa la configuración en `conexion.php`

### Error al Crear/Actualizar
- Asegúrate de proporcionar todos los campos requeridos
- Verifica que los tipos de datos sean correctos

### No se Muestran Resultados
- Abre la consola del navegador (F12) para ver errores
- Verifica que la base de datos tenga datos

## 📝 Ejemplo Completo de Película

```json
{
    "nombre": "Dune: Parte Dos",
    "duracion": 155,
    "url_imagen": "https://example.com/images/dune2.jpg",
    "sinopsis": "Paul Atreides se une a los Fremen...",
    "estado": 1
}
```

---

**Desarrollado para CinePlanet** 🎬
