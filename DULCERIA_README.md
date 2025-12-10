# Módulo de Dulcería - Cineplanet

## Descripción
Módulo completo de dulcería con interfaz idéntica a Cineplanet.com.pe. Permite seleccionar productos organizados por categorías y agregarlos al carrito de compra.

## Archivos Creados

### 1. HTML - `public_html/views/dulceria.html`
Estructura completa con:
- Header con navegación y temporizador
- Panel lateral con resumen de compra
- Pestañas de categorías de productos
- Galería de productos
- Panel inferior con orden actual

### 2. CSS - `public_html/css/dulceria.css`
Estilos completos incluyendo:
- Layout responsivo con flexbox y grid
- Colores corporativos de Cineplanet
- Animaciones suaves
- Estados hover y activos
- Diseño centrado con espacios laterales

### 3. JavaScript - `public_html/js/dulceria.js`
Funcionalidad completa con:
- Datos estáticos de productos (preparado para API)
- Gestión de categorías
- Carrito de compras
- LocalStorage para persistencia
- Temporizador de sesión
- Cálculo de totales

## Variables Principales (Fáciles de Entender)

```javascript
// DATOS DE PRODUCTOS
const productosDulceria = {
    idProducto: Number,           // ID único del producto
    nombreProducto: String,       // Nombre descriptivo
    descripcionProducto: String,  // Descripción completa
    precioProducto: Number,       // Precio en soles
    imagenProducto: String,       // URL de la imagen
    categoria: String             // Categoría del producto
}

// DATOS DE LA PELÍCULA
const datoPelicula = {
    tituloPelicula: String,       // Título de la película
    detallesPelicula: String,     // Formato (2D, 3D, etc)
    nombreCine: String,           // Nombre del cine
    fechaFuncion: String,         // Fecha de la función
    horaFuncion: String,          // Hora de la función
    nombreSala: String,           // Número/nombre de sala
    imagenPelicula: String        // URL del poster
}

// ORDEN DE DULCERÍA
let ordenDulceria = [];           // Array con productos seleccionados
let totalOrden = 0;               // Total acumulado
```

## Conexión con Base de Datos

### Estructura de Tabla Sugerida

```sql
-- Tabla de productos de dulcería
CREATE TABLE productos_dulceria (
    id_producto INT PRIMARY KEY AUTO_INCREMENT,
    nombre_producto VARCHAR(100) NOT NULL,
    descripcion_producto TEXT,
    precio_producto DECIMAL(10,2) NOT NULL,
    imagen_producto VARCHAR(255),
    categoria_producto VARCHAR(50) NOT NULL,
    disponible BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de órdenes de dulcería
CREATE TABLE ordenes_dulceria (
    id_orden INT PRIMARY KEY AUTO_INCREMENT,
    id_compra INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_producto) REFERENCES productos_dulceria(id_producto)
);
```

### API Endpoints a Crear

```php
// public/api/dulceria_api.php

// 1. Obtener productos por categoría
GET /api/dulceria_api.php?accion=obtenerPorCategoria&categoria=promos-dulceras

// 2. Obtener todos los productos disponibles
GET /api/dulceria_api.php?accion=obtenerTodos

// 3. Obtener detalles de un producto
GET /api/dulceria_api.php?accion=obtenerProducto&id=1

// 4. Guardar orden de dulcería
POST /api/dulceria_api.php?accion=guardarOrden
Body: {
    id_compra: 123,
    productos: [
        { id_producto: 1, cantidad: 1, precio: 51.00 },
        { id_producto: 2, cantidad: 2, precio: 28.50 }
    ],
    total: 108.00
}
```

### Modificaciones en JavaScript para Conectar con API

Reemplazar en `dulceria.js`:

```javascript
// ANTES (Datos estáticos)
const productosDulceria = { ... }

// DESPUÉS (Desde API)
let productosDulceria = {};

async function cargarProductosDesdeAPI() {
    try {
        const response = await fetch('../api/dulceria_api.php?accion=obtenerTodos');
        const data = await response.json();
        
        // Organizar por categorías
        productosDulceria = data.reduce((acc, producto) => {
            if (!acc[producto.categoria]) {
                acc[producto.categoria] = [];
            }
            acc[producto.categoria].push(producto);
            return acc;
        }, {});
        
        // Renderizar primera categoría
        renderizarProductos('promos-dulceras');
    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
}

// Llamar al cargar la página
cargarProductosDesdeAPI();
```

### Guardar Orden en Base de Datos

```javascript
// Modificar el botón continuar
btnContinuar.addEventListener('click', async () => {
    try {
        // Guardar orden en la base de datos
        const response = await fetch('../api/dulceria_api.php?accion=guardarOrden', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_compra: localStorage.getItem('idCompra'),
                productos: ordenDulceria.map(p => ({
                    id_producto: p.idProducto,
                    cantidad: 1,
                    precio: p.precioProducto
                })),
                total: totalOrden
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Guardar en localStorage
            localStorage.setItem('ordenDulceria', JSON.stringify(ordenDulceria));
            localStorage.setItem('totalDulceria', totalOrden.toFixed(2));
            
            // Redirigir a pago
            window.location.href = 'pago.html';
        } else {
            alert('Error al guardar la orden: ' + data.mensaje);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar la orden');
    }
});
```

## Flujo de Navegación

```
1. butacas.html → Selección de asientos
2. entradas.html → Selección de tipos de entradas
3. dulceria.html → Selección de productos de dulcería (NUEVO)
4. pago.html → Procesamiento de pago (por crear)
```

## LocalStorage Utilizado

```javascript
// Datos guardados entre páginas
localStorage.setItem('numeroButacasSeleccionadas', 2);
localStorage.setItem('numeroEntradasSeleccionadas', 2);
localStorage.setItem('totalEntradas', '45.00');
localStorage.setItem('ordenDulceria', JSON.stringify(ordenDulceria));
localStorage.setItem('totalDulceria', '108.00');

// Datos de la película
localStorage.setItem('tituloPelicula', 'Nada es lo que Parece 3');
localStorage.setItem('nombreCine', 'CP Tacna');
localStorage.setItem('fechaFuncion', 'Hoy, 21 de Nov, 2025');
// etc...
```

## Características Implementadas

✅ Diseño idéntico a Cineplanet.com.pe
✅ 7 categorías de productos
✅ Galería responsive con grid
✅ Carrito de compras dinámico
✅ Cálculo automático de totales
✅ Temporizador de sesión (4:48)
✅ Persistencia con localStorage
✅ Notificaciones al agregar productos
✅ Navegación entre páginas
✅ Panel de resumen actualizado
✅ Preparado para conexión con BD

## Próximos Pasos

1. Crear `dulceria_api.php` en `public/api/`
2. Crear `dulceria_model.php` en `src/models/`
3. Crear `dulceria_controler.php` en `src/controllers/`
4. Insertar productos de prueba en la BD
5. Actualizar JavaScript para usar API
6. Crear página de pago (`pago.html`)

## Ejemplo de Producto en Base de Datos

```sql
INSERT INTO productos_dulceria (nombre_producto, descripcion_producto, precio_producto, imagen_producto, categoria_producto) 
VALUES (
    'COMBO 2 + 2 DUOMÁX',
    '1 Canchita Gigante + 2 Bebidas (32oz) + 2 Duomáx (44g). *Sabor bebida sujeto a stock / canchita sin refill',
    51.00,
    '/assets/images/combo-duomax.jpg',
    'promos-dulceras'
);
```

## Notas Importantes

- El diseño está centrado con máximo 1400px
- Espacios laterales automáticos
- Todos los precios en soles (S/)
- Las imágenes de productos deben estar en `public_html/assets/images/`
- Los iconos usan FontAwesome 6.0
- Compatible con navegadores modernos

## Contacto

Para dudas o modificaciones, revisar el código JavaScript que está bien comentado y con variables descriptivas.
