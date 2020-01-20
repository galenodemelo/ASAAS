<?php
// Changes to true to display errors and other DEV features
define('DEV_MODE', true);

// Path to the assets from the current URL
define('BASE_PATH', '/testes/asaas/public_html/');
define('PATH_ASSETS', BASE_PATH . 'assets/');
define('PATH_ASSETS_CSS', PATH_ASSETS . 'css/');
define('PATH_ASSETS_JS', PATH_ASSETS . 'js/');
define('PATH_ASSETS_IMG', PATH_ASSETS . 'img/');
define('PATH_ASSETS_VENDOR', PATH_ASSETS . 'vendor/');

// Paths to the source Models, Views and Controllers;
define('PATH_MODELS', __DIR__ . '/../models/');
define('PATH_VIEWS', __DIR__ . '/../views/');
define('PATH_CONTROLLERS', __DIR__ . '/../controllers/');

// List limits
define('RESULTS_PER_PAGE', 10);

// Set the default timezone to GMT -3:00
date_default_timezone_set('America/Sao_Paulo');

// Display all errors (if DEV_MODE is set on Constants)
if (DEV_MODE) {
	error_reporting(E_ALL);
	ini_set('display_errors', true);
}

// Log the error in a file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../log/php-error.log');
