# Guía de Pruebas del Controlador de Funciones

## 📋 Descripción

Este directorio contiene las pruebas para el `FuncionController`. El sistema de pruebas incluye:
- `test_funcion_controller.php` - Ejecuta pruebas en línea de comandos (texto plano)
- `test_funcion_viewer.html` - **Interfaz web visual** para ejecutar y visualizar las pruebas
- `test_funcion_controller_api.php` - API que procesa las pruebas para la interfaz web

## 🚀 Cómo Ejecutar las Pruebas

### ⭐ Desde el navegador (RECOMENDADO):

Accede a la interfaz visual moderna y profesional:
```
http://localhost/cineplanet/src/controllers/test_funcion_viewer.html
```

**Características de la interfaz web:**
- ✅ Visualización elegante y organizada de los resultados
- ✅ Estadísticas en tiempo real (total, exitosos, fallidos)
- ✅ Expandir/contraer secciones y tests individuales
- ✅ Filtrar por estado (todos, exitosos, fallidos)
- ✅ Resaltado de sintaxis JSON
- ✅ Diseño responsive y moderno

### Desde la línea de comandos:

```bash
# Navegar al directorio del controlador
cd src/controllers

# Ejecutar el archivo de pruebas
php test_funcion_controller.php
```

## 🧪 Pruebas Incluidas

El archivo ejecuta **31 pruebas** organizadas en 8 secciones:

### 1. **Pruebas de Lectura (7 pruebas)**
- ✅ Obtener todas las funciones
- ✅ Obtener función por ID
- ✅ Obtener función con ID inválido
- ✅ Obtener funciones por película
- ✅ Obtener funciones por sede
- ✅ Obtener funciones por fecha
- ✅ Obtener funciones con filtros combinados

### 2. **Funciones Avanzadas (4 pruebas)**
- ✅ Obtener funciones agrupadas por película
- ✅ Obtener funciones agrupadas filtradas por ciudad
- ✅ Obtener horarios disponibles para una película
- ✅ Obtener horarios disponibles con filtros

### 3. **Verificación de Disponibilidad (2 pruebas)**
- ✅ Verificar disponibilidad de sala (horario libre)
- ✅ Verificar disponibilidad de sala (horario ocupado)

### 4. **Creación (4 pruebas)**
- ✅ Crear función con datos válidos
- ✅ Crear función con datos incompletos (debe fallar)
- ✅ Crear función con fecha inválida (debe fallar)
- ✅ Crear función con hora inválida (debe fallar)

### 5. **Actualización (5 pruebas)**
- ✅ Actualizar función existente (un campo)
- ✅ Actualizar múltiples campos
- ✅ Verificar que la función fue actualizada
- ✅ Actualizar función inexistente (debe fallar)
- ✅ Actualizar con datos inválidos (debe fallar)

### 6. **Eliminación (3 pruebas)**
- ✅ Eliminar función existente
- ✅ Verificar que la función fue eliminada
- ✅ Eliminar función inexistente (debe fallar)

### 7. **Validación (3 pruebas)**
- ✅ ID no numérico (debe fallar)
- ✅ Fecha con formato incorrecto (debe fallar)
- ✅ Crear función con película inexistente (debe fallar)

### 8. **Casos Especiales (3 pruebas)**
- ✅ Obtener funciones de película sin funciones
- ✅ Filtrar funciones por rango de fechas
- ✅ Obtener horarios sin especificar fecha


## 📊 Interpretación de Resultados

### En la interfaz web:
- **Tarjetas de estadísticas**: Muestra el total de pruebas, exitosas y fallidas
- **✓ PASS** (verde) - La prueba pasó exitosamente
- **✗ FAIL** (rojo) - La prueba falló
- **Secciones colapsables**: Organiza las pruebas por categorías
- **Vista JSON formateada**: Muestra los resultados con colores y formato

### En línea de comandos:
- `✓ PASS` (verde) - La prueba pasó exitosamente
- `✗ FAIL` (rojo) - La prueba falló
- `⚠` (amarillo) - Advertencia o prueba saltada

### Ejemplo de salida exitosa:
```
✓ PASS - Obtener todas las funciones
{
  "success": true,
  "data": [...],
  "total": 10
}
--------------------------------------------------------------------------------
```

### Ejemplo de salida fallida (esperada):
```
✓ PASS - Crear función con datos incompletos (sin hora)
{
  "success": false,
  "message": "El campo 'hora' es obligatorio."
}
--------------------------------------------------------------------------------
```

## ⚙️ Requisitos

- PHP 7.0 o superior
- MySQL/MariaDB
- Servidor web (Apache/Nginx) o PHP built-in server
- Base de datos CinePlanet configurada
- Conexión a la base de datos configurada en `../services/conexion.php`

## 🔧 Configuración

1. Asegúrate de que el servidor web está ejecutándose (XAMPP, WAMP, etc.)
2. Verifica que la base de datos esté activa y accesible
3. Confirma que el archivo `conexion.php` tiene las credenciales correctas
4. Abre `test_funcion_viewer.html` en tu navegador

## 📸 Capturas de Pantalla

La interfaz web incluye:
- **Dashboard principal**: Con botones para ejecutar y limpiar tests
- **Estadísticas en tiempo real**: Total, exitosos y fallidos
- **Filtros**: Para mostrar todos, solo exitosos o solo fallidos
- **Secciones organizadas**: Agrupa las pruebas por categorías
- **Resultados JSON**: Con resaltado de sintaxis y colores

## 🐛 Solución de Problemas

### Error de conexión a la base de datos
- Verifica que MySQL/MariaDB está ejecutándose
- Comprueba las credenciales en `conexion.php`
- Asegúrate de que la base de datos `cineplanet` existe

### La página no carga los tests
- Verifica que `test_funcion_controller_api.php` existe en el mismo directorio
- Comprueba la consola del navegador (F12) para ver errores
- Asegúrate de que PHP está procesando correctamente los archivos

### Errores en las pruebas
- Revisa que todas las tablas necesarias existen en la base de datos
- Verifica que hay datos de prueba en las tablas
- Consulta los mensajes de error específicos en los resultados JSON

## 📝 Notas Importantes

- Las pruebas de creación, actualización y eliminación usan datos de prueba temporales
- Si una función de prueba no se puede crear, algunas pruebas se saltarán
- Los datos de prueba se eliminan automáticamente al final de la ejecución
- La interfaz web es más visual y fácil de usar que la línea de comandos
- Puedes ejecutar las pruebas múltiples veces sin afectar los datos reales

## 👨‍💻 Desarrollo y Mantenimiento

Para agregar nuevas pruebas:
1. Agrega el test en `test_funcion_controller.php` (línea de comandos)
2. Agrega el test correspondiente en `test_funcion_controller_api.php` (API web)
3. Actualiza esta documentación si es necesario

## 📄 Licencia

Este código es parte del proyecto CinePlanet y está sujeto a sus términos de licencia.

### Requisitos:
- PHP 7.4 o superior
- Conexión a la base de datos configurada en `src/services/conexion.php`
- Base de datos poblada con datos de prueba

### Notas Importantes:

1. **Datos de Prueba**: El script crea funciones temporales para pruebas y las elimina al finalizar.

2. **Base de Datos**: Asegúrate de tener datos en las tablas relacionadas:
   - `pelicula` (al menos 1 registro)
   - `sala` (al menos 1 registro)
   - `sede` (al menos 1 registro)
   - `funcion` (al menos 1 registro existente)

3. **Seguridad**: Este archivo está diseñado para entornos de desarrollo. **NO lo uses en producción**.

## 🛠️ Personalización

Puedes modificar las pruebas según tus necesidades:

```php
// Cambiar los IDs usados en las pruebas
printTest(
    "Obtener función por ID (ID=1)",
    $funcionController->getById(1)  // Cambiar el 1 por otro ID
);

// Agregar nuevas pruebas
printTest(
    "Mi prueba personalizada",
    $funcionController->getByPelicula(5)
);
```

## 🐛 Solución de Problemas

### Error: "Error al conectar con la base de datos"
- Verifica que `src/services/conexion.php` esté configurado correctamente
- Asegúrate de que el servidor MySQL esté ejecutándose

### Muchas pruebas fallan con "not found"
- Verifica que existan datos en tu base de datos
- Ejecuta los scripts SQL de población de datos

### Error: "Class 'FuncionController' not found"
- Verifica que el archivo esté en `src/controllers/`
- Verifica que `funcion_controller.php` exista en el mismo directorio

### Error: "función de prueba no se pudo crear"
- Verifica que tengas permisos de INSERT en la base de datos
- Asegúrate de que existan películas y salas válidas en la BD

## 📝 Ejemplo de Uso

```bash
# Ejecutar pruebas
$ php test_funcion_controller.php

✓ Conexión a la base de datos establecida correctamente
================================================================================

========== 1. PRUEBAS DE LECTURA ==========

✓ PASS - Obtener todas las funciones
{
  "success": true,
  "data": [...],
  "total": 10
}
--------------------------------------------------------------------------------

✓ PASS - Obtener función por ID (ID=1)
{
  "success": true,
  "data": {
    "id_funcion": 1,
    "fecha": "2025-10-17",
    "hora": "18:00:00",
    ...
  }
}
--------------------------------------------------------------------------------

...

========== RESUMEN ==========

✓ Todas las pruebas han sido ejecutadas
⚠ Revisa los resultados anteriores para verificar que todo funciona correctamente

Notas:
- Las pruebas de creación, actualización y eliminación usan datos de prueba
- Si una función de prueba no se pudo crear, algunas pruebas se saltarán
- Los datos de prueba se eliminan al final de la ejecución

================================================================================
Pruebas completadas exitosamente!
```

## 📚 Referencias

- [Documentación de la API](./FUNCION_API_DOCS.md)
- [Modelo de Funciones](../models/funcion_model.php)
- [Controlador de Funciones](./funcion_controller.php)

## 🤝 Contribuir

Si encuentras algún error o deseas agregar más pruebas:

1. Agrega tu prueba en la sección correspondiente
2. Usa la función `printTest()` para mantener el formato consistente
3. Documenta qué está probando tu test

```php
// Ejemplo de nueva prueba
printTest(
    "Descripción clara de qué se está probando",
    $funcionController->metodoAPrueba($parametros),
    $resultadoEsperado // true o false
);
```

---

**Última actualización**: 19 de noviembre de 2025
