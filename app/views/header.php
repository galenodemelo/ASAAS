<!DOCTYPE html>
<html lang="pt-br">

<head>
	<!-- Metatags -->
	<meta charset="utf-8">
	<meta name="robots" content="noindex,nofollow">
	<title><?= self::getTitle() ?></title>
	<link href="<?= PATH_ASSETS_IMG . 'favicon.png' ?>" rel="shortcut icon" type="image/x-icon">

	<!-- Material Design Load -->
	<link rel="stylesheet" href="<?= PATH_ASSETS_VENDOR . 'material-design/material.min.css' ?>">
	<script src="<?= PATH_ASSETS_VENDOR . 'material-design/material.min.js' ?>"></script>
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

	<!-- CSS sheets -->
	<link rel="stylesheet" href="<?= PATH_ASSETS_CSS . 'reset.css' ?>">
	<link rel="stylesheet" href="<?= PATH_ASSETS_CSS . 'style.css?' . rand() ?>">
</head>

<body>
	<!-- 
		Sidebar
	 -->
	<nav class="nav shadowed">

		<!-- Logo -->
		<a href="<?= BASE_PATH ?>">
			<img src="<?= PATH_ASSETS_IMG . 'asaas-logo.svg' ?>" alt="Logotipo ASAAS" class="nav__logo">
		</a>

	</nav>

	<!-- 
		Main content
	 -->
	<main role="main" class="app">