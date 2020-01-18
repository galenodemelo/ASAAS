<?php

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
