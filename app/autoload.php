<?php

// Loads the configs and constants
require __DIR__ . '/settings/Configs.php';

/**
 * Autoload the classes required on the app
 */
spl_autoload_register(function ($className) {

	// Prevents a backlash error on using namespaces
	require __DIR__ . '/' . str_replace('\\', '/', $className) . '.php';
});
