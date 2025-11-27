<?php
/**
 * Archivo de prueba para FuncionController
 * 
 * Este archivo permite probar todas las funcionalidades del controlador de funciones
 * sin necesidad de hacer peticiones HTTP a la API.
 * 
 * Ejecutar: php test_funcion_controller.php
 */

// Incluir archivos necesarios
require_once __DIR__ . "/../services/conexion.php";
require_once __DIR__ . "/funcion_controller.php";

// Configurar salida para mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Colores para la consola (opcional)
class Colors {
    public static $GREEN = "\033[0;32m";
    public static $RED = "\033[0;31m";
    public static $YELLOW = "\033[1;33m";
    public static $BLUE = "\033[0;34m";
    public static $NC = "\033[0m"; // No Color
}

// Función auxiliar para imprimir resultados
function printTest($nombre, $resultado, $esperado = true) {
    $color = Colors::$GREEN;
    $estado = "✓ PASS";
    
    if (isset($resultado['success']) && $resultado['success'] == $esperado) {
        $color = Colors::$GREEN;
        $estado = "✓ PASS";
    } else {
        $color = Colors::$RED;
        $estado = "✗ FAIL";
    }
    
    echo $color . $estado . Colors::$NC . " - " . $nombre . "\n";
    echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    echo str_repeat("-", 80) . "\n\n";
}

function printSection($titulo) {
    echo "\n" . Colors::$BLUE . "========== " . $titulo . " ==========" . Colors::$NC . "\n\n";
}

// Inicializar el controlador
try {
    $funcionController = new FuncionController($conn);
    echo Colors::$GREEN . "✓ Conexión a la base de datos establecida correctamente\n" . Colors::$NC;
    echo str_repeat("=", 80) . "\n";
} catch (Exception $e) {
    echo Colors::$RED . "✗ Error al conectar con la base de datos: " . $e->getMessage() . Colors::$NC . "\n";
    exit(1);
}

// =============================================================================
// 1. PRUEBAS DE LECTURA (GET)
// =============================================================================

printSection("1. PRUEBAS DE LECTURA");

// Test 1: Obtener todas las funciones
printTest(
    "Obtener todas las funciones",
    $funcionController->getAll()
);

// Test 2: Obtener función por ID
printTest(
    "Obtener función por ID (ID=1)",
    $funcionController->getById(1)
);

// Test 3: Obtener función con ID inválido
printTest(
    "Obtener función con ID inválido (ID=9999)",
    $funcionController->getById(9999),
    false
);

// Test 4: Obtener funciones por película
printTest(
    "Obtener funciones por película (ID película=1)",
    $funcionController->getByPelicula(1)
);

// Test 5: Obtener funciones por sede
printTest(
    "Obtener funciones por sede (ID sede=1)",
    $funcionController->getBySede(1)
);

// Test 6: Obtener funciones por fecha
printTest(
    "Obtener funciones por fecha (2025-10-17)",
    $funcionController->getByFecha("2025-10-17")
);

// Test 7: Obtener funciones con filtros combinados
printTest(
    "Obtener funciones con filtros (película=1, fecha desde=2025-10-17)",
    $funcionController->getByFilters([
        "id_pelicula" => 1,
        "fecha_desde" => "2025-10-17"
    ])
);

// =============================================================================
// 2. PRUEBAS DE FUNCIONES AVANZADAS
// =============================================================================

printSection("2. PRUEBAS DE FUNCIONES AVANZADAS");

// Test 8: Obtener funciones agrupadas por película
printTest(
    "Obtener funciones agrupadas por película",
    $funcionController->getFuncionesAgrupadasPorPelicula()
);

// Test 9: Obtener funciones agrupadas por película filtradas por ciudad
printTest(
    "Obtener funciones agrupadas por película (Ciudad=1)",
    $funcionController->getFuncionesAgrupadasPorPelicula([
        "id_ciudad" => 1
    ])
);

// Test 10: Obtener horarios disponibles para una película
printTest(
    "Obtener horarios disponibles (película=1)",
    $funcionController->getHorariosDisponibles(1)
);

// Test 11: Obtener horarios disponibles con filtros
printTest(
    "Obtener horarios disponibles (película=1, fecha=2025-10-17)",
    $funcionController->getHorariosDisponibles(1, "2025-10-17")
);

// =============================================================================
// 3. PRUEBAS DE VERIFICACIÓN DE DISPONIBILIDAD
// =============================================================================

printSection("3. PRUEBAS DE VERIFICACIÓN DE DISPONIBILIDAD");

// Test 12: Verificar disponibilidad de sala (horario libre)
printTest(
    "Verificar disponibilidad de sala (horario libre: sala=1, 2025-12-25 15:00)",
    $funcionController->verificarDisponibilidadSala(
        1,              // id_sala
        "2025-12-25",   // fecha
        "15:00:00",     // hora
        120             // duración en minutos
    )
);

// Test 13: Verificar disponibilidad de sala (horario ocupado)
printTest(
    "Verificar disponibilidad de sala (horario ocupado: sala=1, 2025-10-17 18:00)",
    $funcionController->verificarDisponibilidadSala(
        1,              // id_sala
        "2025-10-17",   // fecha
        "18:00:00",     // hora
        155             // duración en minutos
    )
);

// =============================================================================
// 4. PRUEBAS DE CREACIÓN (POST)
// =============================================================================

printSection("4. PRUEBAS DE CREACIÓN");

// Test 14: Crear función con datos válidos
$nuevaFuncion = $funcionController->create([
    "fecha" => "2025-12-01",
    "hora" => "20:00:00",
    "id_pelicula" => 1,
    "id_sala" => 1
]);
printTest("Crear función con datos válidos", $nuevaFuncion);

// Guardar el ID de la función creada para pruebas posteriores
$idFuncionCreada = isset($nuevaFuncion['id_funcion']) ? $nuevaFuncion['id_funcion'] : null;

// Test 15: Intentar crear función con datos incompletos
printTest(
    "Crear función con datos incompletos (sin hora)",
    $funcionController->create([
        "fecha" => "2025-12-01",
        "id_pelicula" => 1,
        "id_sala" => 1
    ]),
    false
);

// Test 16: Intentar crear función con fecha inválida
printTest(
    "Crear función con fecha inválida",
    $funcionController->create([
        "fecha" => "2025-13-45", // Fecha inválida
        "hora" => "20:00:00",
        "id_pelicula" => 1,
        "id_sala" => 1
    ]),
    false
);

// Test 17: Intentar crear función con hora inválida
printTest(
    "Crear función con hora inválida",
    $funcionController->create([
        "fecha" => "2025-12-01",
        "hora" => "25:70:00", // Hora inválida
        "id_pelicula" => 1,
        "id_sala" => 1
    ]),
    false
);

// =============================================================================
// 5. PRUEBAS DE ACTUALIZACIÓN (PUT)
// =============================================================================

printSection("5. PRUEBAS DE ACTUALIZACIÓN");

if ($idFuncionCreada) {
    // Test 18: Actualizar función existente
    printTest(
        "Actualizar función (cambiar hora)",
        $funcionController->update($idFuncionCreada, [
            "hora" => "21:30:00"
        ])
    );

    // Test 19: Actualizar múltiples campos
    printTest(
        "Actualizar función (cambiar fecha y hora)",
        $funcionController->update($idFuncionCreada, [
            "fecha" => "2025-12-02",
            "hora" => "19:00:00"
        ])
    );

    // Test 20: Verificar que la función fue actualizada
    printTest(
        "Verificar función actualizada",
        $funcionController->getById($idFuncionCreada)
    );
} else {
    echo Colors::$YELLOW . "⚠ Saltando pruebas de actualización (no se pudo crear función de prueba)\n" . Colors::$NC;
}

// Test 21: Intentar actualizar función inexistente
printTest(
    "Actualizar función inexistente (ID=99999)",
    $funcionController->update(99999, [
        "hora" => "22:00:00"
    ]),
    false
);

// Test 22: Intentar actualizar con datos inválidos
printTest(
    "Actualizar con fecha inválida",
    $funcionController->update(1, [
        "fecha" => "fecha-invalida"
    ]),
    false
);

// =============================================================================
// 6. PRUEBAS DE ELIMINACIÓN (DELETE)
// =============================================================================

printSection("6. PRUEBAS DE ELIMINACIÓN");

if ($idFuncionCreada) {
    // Test 23: Eliminar función existente
    printTest(
        "Eliminar función de prueba (ID=$idFuncionCreada)",
        $funcionController->delete($idFuncionCreada)
    );

    // Test 24: Verificar que la función fue eliminada
    printTest(
        "Verificar que la función fue eliminada",
        $funcionController->getById($idFuncionCreada),
        false
    );
} else {
    echo Colors::$YELLOW . "⚠ Saltando pruebas de eliminación (no se pudo crear función de prueba)\n" . Colors::$NC;
}

// Test 25: Intentar eliminar función inexistente
printTest(
    "Eliminar función inexistente (ID=99999)",
    $funcionController->delete(99999),
    false
);

// =============================================================================
// 7. PRUEBAS DE VALIDACIÓN
// =============================================================================

printSection("7. PRUEBAS DE VALIDACIÓN");

// Test 26: ID no numérico
printTest(
    "Obtener función con ID no numérico",
    $funcionController->getById("abc"),
    false
);

// Test 27: Fecha con formato incorrecto
printTest(
    "Obtener funciones con fecha en formato incorrecto",
    $funcionController->getByFecha("17-10-2025"),
    false
);

// Test 28: Crear función con película inexistente
printTest(
    "Crear función con película inexistente",
    $funcionController->create([
        "fecha" => "2025-12-01",
        "hora" => "20:00:00",
        "id_pelicula" => 99999,
        "id_sala" => 1
    ]),
    false
);

// =============================================================================
// 8. PRUEBAS DE CASOS ESPECIALES
// =============================================================================

printSection("8. PRUEBAS DE CASOS ESPECIALES");

// Test 29: Obtener funciones de película sin funciones
printTest(
    "Obtener funciones de película sin funciones registradas",
    $funcionController->getByPelicula(99999)
);

// Test 30: Filtrar funciones con rango de fechas
printTest(
    "Filtrar funciones por rango de fechas",
    $funcionController->getByFilters([
        "fecha_desde" => "2025-10-15",
        "fecha_hasta" => "2025-10-20"
    ])
);

// Test 31: Obtener horarios sin especificar fecha (desde hoy)
printTest(
    "Obtener horarios disponibles sin especificar fecha (desde hoy)",
    $funcionController->getHorariosDisponibles(1)
);

// =============================================================================
// RESUMEN FINAL
// =============================================================================

printSection("RESUMEN");
echo Colors::$GREEN . "✓ Todas las pruebas han sido ejecutadas\n" . Colors::$NC;
echo Colors::$YELLOW . "⚠ Revisa los resultados anteriores para verificar que todo funciona correctamente\n" . Colors::$NC;
echo Colors::$BLUE . "\nNotas:\n" . Colors::$NC;
echo "- Las pruebas de creación, actualización y eliminación usan datos de prueba\n";
echo "- Si una función de prueba no se pudo crear, algunas pruebas se saltarán\n";
echo "- Los datos de prueba se eliminan al final de la ejecución\n";
echo "\n" . str_repeat("=", 80) . "\n";
echo Colors::$GREEN . "Pruebas completadas exitosamente!\n" . Colors::$NC;
