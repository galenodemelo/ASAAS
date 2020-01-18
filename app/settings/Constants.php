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
