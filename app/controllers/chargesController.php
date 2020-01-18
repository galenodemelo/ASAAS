<?php

namespace Controllers;

use \Classes\Loader;
use \Models\Charges;

class ChargesController
{
	/**
	 * Index with the list of charges
	 */
	public function indexAction()
	{
		// Gets all the charges to iterates on the view
		$charges = new Charges();
		$charges_list = $charges->getChargesList();

		// Loads the views
		$page_content = PATH_VIEWS . 'charges/index.php';
		Loader::setTitle('Gestão de Cobranças');
		Loader::loadView($page_content);
	}

	/**
	 * Form to create a new charge
	 */
	public function newAction()
	{
		// Loads the views
		$page_content = PATH_VIEWS . 'charges/form.php';
		Loader::setTitle('Nova cobrança');
		Loader::loadView($page_content);
	}
}

// Automatically instantiates
$controller = new ChargesController();
