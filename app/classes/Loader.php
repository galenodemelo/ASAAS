<?php

namespace Classes;

use Classes\Helpers;

class Loader
{
	private static $title = 'Gestão de Cobranças :: ASAAS';
	private static $v;

	/**
	 * Loads the view
	 * @param string
	 */
	public static function loadView(string $pageContent)
	{
		require PATH_VIEWS . 'header.php';
		require $pageContent;
		require PATH_VIEWS . 'footer.php';

		// If theres a message to be shown
		if (!empty($_GET['success']) && !empty($_GET['msg'])) {
			$msg = filter_input(INPUT_GET, 'msg', FILTER_SANITIZE_STRING);

			self::popMessage($msg);
		}
	}

	/**
	 * *************************************
	 * SETTERS
	 * *************************************
	 */

	/**
	 * Set the <title> content tag
	 * @param string title
	 */
	public static function setTitle(string $title)
	{
		self::$title = Helpers::sanitizeString($title) . ' :: ASAAS';
	}

	/**
	 * Set the vars to be used on view
	 * @param array vars
	 */
	public static function setVars(array $vars)
	{
		self::$v = $vars;
	}

	/**
	 * Create the pagination links and set the active one
	 * @param int $page_number
	 * @param int $page_active
	 */
	public static function createPagination(int $page_number = 1, int $page_active = 1)
	{
		// Creates the pagination URL
		$url = Helpers::getThisUrl();

		// If there's query strings on the current URL, adds parameter with ampersand
		$url .= parse_url($url, PHP_URL_QUERY) ?
			'&page=' :
			'?page=';

		$pagination = '<nav class="app__pagination">';
		for ($i = 1; $i <= $page_number; $i++) {
			$class = $i == $page_active ? 'app__pagination__item--active' : 'app__pagination__item';

			$pagination .= '<a href="' . $url . $i . '" class="' . $class . '">' . $i . '</a>';
		}
		$pagination .= '</nav>';

		self::$v['pagination'] = $pagination;
	}

	/**
	 * Trigger message
	 * @param string $msg
	 */
	public static function popMessage(string $msg)
	{
		echo '
			<script>
				setTimeout(() => {	
					popMessage("' . $msg . '");
				}, 1000);
			</script>
		';
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
