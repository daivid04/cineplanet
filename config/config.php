<?php
/**
 * Archivo de configuracion global
 * Cineplanet - Sistema de Gestion
 */

// Zona horaria
date_default_timezone_set('America/Lima');

// Configuracion de errores (cambiar a 0 en produccion)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Rutas base
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public_html');
define('SRC_PATH', BASE_PATH . '/src');

// URL base (ajustar segun entorno)
define('BASE_URL', '/');
define('API_URL', '/api/');
define('ADMIN_URL', '/admin/');

// Configuracion de la aplicacion
define('APP_NAME', 'Cineplanet Admin');
define('APP_VERSION', '1.0.0');
