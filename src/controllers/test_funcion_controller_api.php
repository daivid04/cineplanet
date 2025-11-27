<?php
/**
 * API de prueba para FuncionController
 * 
 * Este archivo ejecuta las pruebas y devuelve los resultados en formato JSON
 * para ser consumidos por test_funcion_viewer.html
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Incluir archivos necesarios
require_once __DIR__ . "/../services/conexion.php";
require_once __DIR__ . "/funcion_controller.php";

// Configurar salida para mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Array para almacenar los resultados
$testResults = [
    'sections' => [],
    'summary' => [
        'total' => 0,
        'passed' => 0,
        'failed' => 0,
        'timestamp' => date('Y-m-d H:i:s')
    ]
];

// Función auxiliar para registrar un test
function addTest(&$section, $nombre, $resultado, $esperado = true) {
    global $testResults;
    
    $passed = isset($resultado['success']) && $resultado['success'] == $esperado;
    
    $section['tests'][] = [
        'name' => $nombre,
        'result' => $resultado,
        'expected' => $esperado,
        'passed' => $passed
    ];
    
    $testResults['summary']['total']++;
    if ($passed) {
        $testResults['summary']['passed']++;
    } else {
        $testResults['summary']['failed']++;
    }
}

// Función auxiliar para crear una sección
function createSection($titulo) {
    return [
        'title' => $titulo,
        'tests' => []
    ];
}

try {
    // Inicializar el controlador
    $funcionController = new FuncionController($conn);
    
    // =============================================================================
    // 1. PRUEBAS DE LECTURA (GET)
    // =============================================================================
    
    $section1 = createSection('1. Pruebas de Lectura (GET)');
    
    addTest($section1, "Obtener todas las funciones", $funcionController->getAll());
    addTest($section1, "Obtener función por ID (ID=1)", $funcionController->getById(1));
    addTest($section1, "Obtener función con ID inválido (ID=9999)", $funcionController->getById(9999), false);
    addTest($section1, "Obtener funciones por película (ID película=1)", $funcionController->getByPelicula(1));
    addTest($section1, "Obtener funciones por sede (ID sede=1)", $funcionController->getBySede(1));
    addTest($section1, "Obtener funciones por fecha (2025-10-17)", $funcionController->getByFecha("2025-10-17"));
    addTest($section1, "Obtener funciones con filtros combinados", $funcionController->getByFilters([
        "id_pelicula" => 1,
        "fecha_desde" => "2025-10-17"
    ]));
    
    $testResults['sections'][] = $section1;
    
    // =============================================================================
    // 2. PRUEBAS DE FUNCIONES AVANZADAS
    // =============================================================================
    
    $section2 = createSection('2. Pruebas de Funciones Avanzadas');
    
    addTest($section2, "Obtener funciones agrupadas por película", $funcionController->getFuncionesAgrupadasPorPelicula());
    addTest($section2, "Obtener funciones agrupadas por película (Ciudad=1)", $funcionController->getFuncionesAgrupadasPorPelicula([
        "id_ciudad" => 1
    ]));
    addTest($section2, "Obtener horarios disponibles (película=1)", $funcionController->getHorariosDisponibles(1));
    addTest($section2, "Obtener horarios disponibles con filtros", $funcionController->getHorariosDisponibles(1, "2025-10-17"));
    
    $testResults['sections'][] = $section2;
    
    // =============================================================================
    // 3. PRUEBAS DE VERIFICACIÓN DE DISPONIBILIDAD
    // =============================================================================
    
    $section3 = createSection('3. Pruebas de Verificación de Disponibilidad');
    
    addTest($section3, "Verificar disponibilidad de sala (horario libre)", $funcionController->verificarDisponibilidadSala(
        1,              // id_sala
        "2025-12-25",   // fecha
        "15:00:00",     // hora
        120             // duración en minutos
    ));
    
    addTest($section3, "Verificar disponibilidad de sala (horario ocupado)", $funcionController->verificarDisponibilidadSala(
        1,              // id_sala
        "2025-10-17",   // fecha
        "18:00:00",     // hora
        155             // duración en minutos
    ));
    
    $testResults['sections'][] = $section3;
    
    // =============================================================================
    // 4. PRUEBAS DE CREACIÓN (POST)
    // =============================================================================
    
    $section4 = createSection('4. Pruebas de Creación (POST)');
    
    $nuevaFuncion = $funcionController->create([
        "fecha" => "2025-12-01",
        "hora" => "20:00:00",
        "id_pelicula" => 1,
        "id_sala" => 1
    ]);
    addTest($section4, "Crear función con datos válidos", $nuevaFuncion);
    
    $idFuncionCreada = isset($nuevaFuncion['id_funcion']) ? $nuevaFuncion['id_funcion'] : null;
    
    addTest($section4, "Crear función con datos incompletos (sin hora)", $funcionController->create([
        "fecha" => "2025-12-01",
        "id_pelicula" => 1,
        "id_sala" => 1
    ]), false);
    
    addTest($section4, "Crear función con fecha inválida", $funcionController->create([
        "fecha" => "2025-13-45",
        "hora" => "20:00:00",
        "id_pelicula" => 1,
        "id_sala" => 1
    ]), false);
    
    addTest($section4, "Crear función con hora inválida", $funcionController->create([
        "fecha" => "2025-12-01",
        "hora" => "25:70:00",
        "id_pelicula" => 1,
        "id_sala" => 1
    ]), false);
    
    $testResults['sections'][] = $section4;
    
    // =============================================================================
    // 5. PRUEBAS DE ACTUALIZACIÓN (PUT)
    // =============================================================================
    
    $section5 = createSection('5. Pruebas de Actualización (PUT)');
    
    if ($idFuncionCreada) {
        addTest($section5, "Actualizar función (cambiar hora)", $funcionController->update($idFuncionCreada, [
            "hora" => "21:30:00"
        ]));
        
        addTest($section5, "Actualizar función (cambiar fecha y hora)", $funcionController->update($idFuncionCreada, [
            "fecha" => "2025-12-02",
            "hora" => "19:00:00"
        ]));
        
        addTest($section5, "Verificar función actualizada", $funcionController->getById($idFuncionCreada));
    }
    
    addTest($section5, "Actualizar función inexistente (ID=99999)", $funcionController->update(99999, [
        "hora" => "22:00:00"
    ]), false);
    
    addTest($section5, "Actualizar con fecha inválida", $funcionController->update(1, [
        "fecha" => "fecha-invalida"
    ]), false);
    
    $testResults['sections'][] = $section5;
    
    // =============================================================================
    // 6. PRUEBAS DE ELIMINACIÓN (DELETE)
    // =============================================================================
    
    $section6 = createSection('6. Pruebas de Eliminación (DELETE)');
    
    if ($idFuncionCreada) {
        addTest($section6, "Eliminar función de prueba (ID=$idFuncionCreada)", $funcionController->delete($idFuncionCreada));
        addTest($section6, "Verificar que la función fue eliminada", $funcionController->getById($idFuncionCreada), false);
    }
    
    addTest($section6, "Eliminar función inexistente (ID=99999)", $funcionController->delete(99999), false);
    
    $testResults['sections'][] = $section6;
    
    // =============================================================================
    // 7. PRUEBAS DE VALIDACIÓN
    // =============================================================================
    
    $section7 = createSection('7. Pruebas de Validación');
    
    addTest($section7, "Obtener función con ID no numérico", $funcionController->getById("abc"), false);
    addTest($section7, "Obtener funciones con fecha en formato incorrecto", $funcionController->getByFecha("17-10-2025"), false);
    addTest($section7, "Crear función con película inexistente", $funcionController->create([
        "fecha" => "2025-12-01",
        "hora" => "20:00:00",
        "id_pelicula" => 99999,
        "id_sala" => 1
    ]), false);
    
    $testResults['sections'][] = $section7;
    
    // =============================================================================
    // 8. PRUEBAS DE CASOS ESPECIALES
    // =============================================================================
    
    $section8 = createSection('8. Pruebas de Casos Especiales');
    
    addTest($section8, "Obtener funciones de película sin funciones registradas", $funcionController->getByPelicula(99999));
    addTest($section8, "Filtrar funciones por rango de fechas", $funcionController->getByFilters([
        "fecha_desde" => "2025-10-15",
        "fecha_hasta" => "2025-10-20"
    ]));
    addTest($section8, "Obtener horarios disponibles sin especificar fecha", $funcionController->getHorariosDisponibles(1));
    
    $testResults['sections'][] = $section8;
    
    // Devolver resultados
    echo json_encode($testResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
