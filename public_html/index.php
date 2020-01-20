<?php

// Set the header charset

use Classes\DBConnect;

header('Content-Type: text/html; charset=utf-8');

// Loads constants, configs and classes
require __DIR__ . '/../app/autoload.php';

/**
 * Handles the URL parameters
 */
define('MODULE',     filter_input(INPUT_GET, 'module', FILTER_SANITIZE_STRING)     ?? 'charges');
define('ACTION',     filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING)     ?? 'index');
define('IDENTIFIER', filter_input(INPUT_GET, 'identifier', FILTER_SANITIZE_STRING) ?? '');

try {
	$controllerFile = PATH_CONTROLLERS . MODULE . 'Controller.php';

	// Verify if the module exists
	if (is_file($controllerFile)) {

		/**
		 * Automatically loads the controller file
		 * and instantiates it to a $controller var
		 */
		require $controllerFile;

		$action = ACTION . 'Action';

		// Verify if is a valid action and triggers it
		if (method_exists($controller, $action)) {
			$controller->$action();
		} else {
			throw new Exception('Ação não permitida');
		}
	} else {
		throw new Exception('Página não encontrada', 404);
	}
} catch (Exception $e) {
	error_log($e->getMessage(), 0);
	echo $e->getMessage();
}

DBConnect::disconnect();
