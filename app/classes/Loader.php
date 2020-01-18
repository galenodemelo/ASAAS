<?php

namespace Classes;

use Classes\Helpers;

class Loader
{
	public static $title = 'Gestão de Cobranças :: ASAAS';

	/**
	 * Loads the view
	 * @param string
	 */
	public static function loadView(string $pageContent)
	{
		require PATH_VIEWS . 'header.php';
		require $pageContent;
		require PATH_VIEWS . 'footer.php';
	}

	/**
	 * *************************************
	 * SETTERS
	 * *************************************
	 */
	public static function setTitle(string $title)
	{
		self::$title = Helpers::sanitizeString($title) . ' :: ASAAS';
	}

	/**
	 * *************************************
	 * GETTERS
	 * *************************************
	 */
	public static function getTitle(): string
	{
		return self::$title;
	}
}
