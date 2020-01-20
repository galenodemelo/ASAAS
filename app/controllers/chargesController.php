<?php

namespace Controllers;

use \Classes\DBConnect;
use \Classes\Helpers as Helpers;
use \Classes\Loader;
use \Models\Charges;
use \Models\ChargesStatus;
use \Models\Customers;
use \Models\PaymentMethods;

class ChargesController
{
	/**
	 * Index with the list of charges
	 */
	public function indexAction()
	{

		// Get charges status options for filter
		$charges_status = new ChargesStatus();
		$charges_status_list = $charges_status->listAll();

		// Get payment methods for filter
		$payment_methods = new PaymentMethods();
		$payment_methods_list = $payment_methods->listAll();

		// Current page number
		$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;

		// Calculates the offset (considering base zero, needs to reduce the page number by one)
		$offset = ($page - 1) * RESULTS_PER_PAGE;

		// Get all the charges to iterates on the view
		$charges = new Charges();
		$charges_list = $charges->listAll(RESULTS_PER_PAGE, $offset);

		// Create pagination
		$total_results = $charges->getNumberResults();
		$page_number = Helpers::countPages($total_results);

		// View configs
		$page_title = 'Lista de cobranças';
		$page_content = PATH_VIEWS . 'charges/index.php';
		$page_vars = array(

			// Page h1
			'h1' => 'Lista de cobranças',

			// Options to filter
			'charges_status_list' => $charges_status_list,
			'payment_methods_list' => $payment_methods_list,

			// Pagination configs
			'page_number' => $page_number,
			'page_active' => $page,

			// List of charges
			'charges_list' => $charges_list
		);

		// Loads the views
		Loader::setTitle($page_title);
		Loader::setVars($page_vars);
		Loader::createPagination($page_number, $page);
		Loader::loadView($page_content);
	}

	public function searchAction()
	{
		// Get charges status options for filter
		$charges_status = new ChargesStatus();
		$charges_status_list = $charges_status->listAll();

		// Get payment methods for filter
		$payment_methods = new PaymentMethods();
		$payment_methods_list = $payment_methods->listAll();

		// Current page number
		$page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;

		// Calculates the offset (considering base zero, needs to reduce the page number by one)
		$offset = ($page - 1) * RESULTS_PER_PAGE;

		// Get all the search parameters
		$searchParameters = Helpers::getFormData(array(
			'name_email',
			'initial_date',
			'final_date',
			'payment_methods',
			'status'
		), INPUT_GET);

		// Search by name email
		$name_email = !empty($searchParameters['name_email']) ?
			Helpers::sanitizeString($searchParameters['name_email']) :
			'';

		// Search by dates
		$initial_date = Helpers::dbDateFormat($searchParameters['initial_date']);
		$final_date = Helpers::dbDateFormat($searchParameters['final_date']);

		// Search by payment methods or charge status
		$payment_methods = (int) $searchParameters['payment_methods'];
		$status = (int) $searchParameters['status'];

		// Get all the charges to iterates on the view
		$charges = new Charges();

		$charges_list = $charges->listAll(
			RESULTS_PER_PAGE,
			$offset,
			$name_email,
			$initial_date,
			$final_date,
			$payment_methods,
			$status
		);

		// Create pagination
		$total_results = $charges->getNumberResults();
		$page_number = Helpers::countPages($total_results);

		// View configs
		$page_title = 'Lista de cobranças';
		$page_content = PATH_VIEWS . 'charges/index.php';
		$page_vars = array(

			// Page h1
			'h1' => 'Lista de cobranças',

			// Options to filter
			'charges_status_list' => $charges_status_list,
			'payment_methods_list' => $payment_methods_list,

			// Pagination configs
			'page_number' => $page_number,
			'page_active' => $page,

			// List of charges
			'charges_list' => $charges_list,

			// Search paramenters
			'search_parameters' => $searchParameters
		);

		// Loads the views
		Loader::setTitle($page_title);
		Loader::setVars($page_vars);
		Loader::createPagination($page_number, $page);
		Loader::loadView($page_content);
	}
	/**
	 * Form to create a new charge
	 */
	public function newAction()
	{

		// Get payment methods
		$payment_methods = new PaymentMethods();
		$payment_methods_list = $payment_methods->listAll();


		// View configs
		$page_title = 'Nova cobrança';
		$page_content = PATH_VIEWS . 'charges/form.php';
		$page_vars = array(

			// Page h1
			'h1' => 'Adicionar cobrança',

			// Form action
			'form_action' => BASE_PATH . 'charges/create/',

			// Form action
			'send_button_label' => 'Adicionar cobrança',

			// Options to form
			'payment_methods_list' => $payment_methods_list,

			// Values for the input fields
			'values' => array(

				// Due date with 3 days more from today
				'due_date' => date('d/m/Y', strtotime('+3days'))

			)
		);

		// Loads the views
		Loader::setTitle($page_title);
		Loader::setVars($page_vars);
		Loader::loadView($page_content);
	}

	/**
	 * Adds a new charge based on data which came form form
	 */
	public function createAction()
	{
		// Get form data
		$data = Helpers::getFormData(array(
			'customer_name',
			'customer_email',
			'customer_cpf',
			'due_date',
			'charges_status',
			'payment_methods',
			'charge_value',
			'description'
		));

		/**
		 * Check if the customer is already registered
		 * If true, gets the customer id
		 * Else, saves on database and get it's id
		 */
		$customer = new Customers();
		if ($customer->findByEmailCpf($data['customer_email'], $data['customer_cpf'])) {

			// Gets customer id
			$data['customer_id'] = $customer->getId();

			// Defines if needs to update customer data
			$updateCustomer = false;

			// If user changed the name of customer
			if ($customer->getName() != $data['customer_name'] && !empty($data['customer_name'])) {
				$customer->setName($data['customer_name']);
				$updateCustomer = true;
			}

			// If user added a email or changed it
			if ($customer->getEmail() != $data['customer_email'] && !empty($data['customer_email'])) {
				$customer->setEmail($data['customer_email']);
				$updateCustomer = true;
			}

			// If user added a cpf or changed it
			if ($customer->getCpf() != $data['customer_cpf'] && !empty($data['customer_cpf'])) {
				$customer->setCpf($data['customer_cpf']);
				$updateCustomer = true;
			}

			// If some data changed, update the customer
			if ($updateCustomer) {
				$customer->update();
			}
		} else {

			// Saves customer data
			$customer->setName($data['customer_name'])
				->setEmail($data['customer_email'])
				->setCpf($data['customer_cpf']);

			if ($customer->create()) {
				$data['customer_id'] = DBConnect::getLastId();
			} else {
				die('Falha ao cadastrar cliente');
			}
		}

		/**
		 * Creates the charge
		 */
		$charge = new Charges();
		$charge->setDueDate($data['due_date'])
			->setValue($data['charge_value'])
			->setDescription($data['description'])
			->setCustomersId($data['customer_id'])
			->setPaymentMethodId($data['payment_methods']);

		$success = $charge->create();
		$msg = $success ?
			'Cobrança cadastrada com sucesso' :
			'Falha ao cadastrar cobrança. Tente novamente';

		header('Location: ' . BASE_PATH . '?success=' . $success . '&msg=' . $msg);
		exit;
	}

	/**
	 * Form to edit a charge
	 */
	public function editAction()
	{
		// Get payment methods
		$payment_methods = new PaymentMethods();
		$payment_methods_list = $payment_methods->listAll();

		// Get charge info
		$charge = new Charges();
		if ($charge->findById(IDENTIFIER)) {

			// Passes data to the form
			$values = array(
				'due_date' => $charge->getDueDate(),
				'charges_status' => $charge->getChargesStatusId(),
				'payment_method' => $charge->getPaymentMethodId(),
				'charge_value' => $charge->getValue(),
				'description' => $charge->getDescription()
			);

			// Fetches customer data
			$customer = new Customers();
			if ($customer->findById($charge->getCustomersId())) {
				$values = array_merge($values, array(
					'customer_name' => $customer->getName(),
					'customer_email' => $customer->getEmail(),
					'customer_cpf' => $customer->getCpf(),
				));
			}
		} else {
			$msg = 'Cobrança não encontrada';
			header('Location: ' . BASE_PATH . '?success=0&msg=' . $msg);
			exit;
		}

		// View configs
		$page_title = 'Editar cobrança';
		$page_content = PATH_VIEWS . 'charges/form.php';
		$page_vars = array(

			// Page h1
			'h1' => 'Editando cobrança de ' . $customer->getName(),

			// Form action
			'form_action' => BASE_PATH . 'charges/update/' . $charge->getId(),

			// Form action
			'send_button_label' => 'Salvar',

			// Options to form
			'payment_methods_list' => $payment_methods_list,

			// Values for the input fields
			'values' => $values
		);

		// Loads the views
		Loader::setTitle($page_title);
		Loader::setVars($page_vars);
		Loader::loadView($page_content);
	}

	/**
	 * Adds a new charge based on data which came form form
	 */
	public function updateAction()
	{
		// Get form data
		$data = Helpers::getFormData(array(
			'customer_name',
			'customer_email',
			'customer_cpf',
			'due_date',
			'charges_status',
			'payment_methods',
			'charge_value',
			'description'
		));

		/**
		 * Check if the customer is already registered
		 * If true, gets the customer id
		 * Else, saves on database and get it's id
		 */
		$customer = new Customers();
		if ($customer->findByEmailCpf($data['customer_email'], $data['customer_cpf'])) {

			// Gets customer id
			$data['customer_id'] = $customer->getId();

			// Defines if needs to update customer data
			$updateCustomer = false;

			// If user changed the name of customer
			if ($customer->getName() != $data['customer_name'] && !empty($data['customer_name'])) {
				$customer->setName($data['customer_name']);
				$updateCustomer = true;
			}

			// If user added a email or changed it
			if ($customer->getEmail() != $data['customer_email'] && !empty($data['customer_email'])) {
				$customer->setEmail($data['customer_email']);
				$updateCustomer = true;
			}

			// If user added a cpf or changed it
			if ($customer->getCpf() != $data['customer_cpf'] && !empty($data['customer_cpf'])) {
				$customer->setCpf($data['customer_cpf']);
				$updateCustomer = true;
			}

			// If some data changed, update the customer
			if ($updateCustomer) {
				$customer->update();
			}
		} else {

			// Saves customer data
			$customer->setName($data['customer_name'])
				->setEmail($data['customer_email'])
				->setCpf($data['customer_cpf']);

			if ($customer->create()) {
				$data['customer_id'] = DBConnect::getLastId();
			} else {
				die('Falha ao cadastrar cliente');
			}
		}

		/**
		 * Updates the charge
		 */
		$charge = new Charges();
		$charge->setId(IDENTIFIER)
			->setDueDate($data['due_date'])
			->setValue($data['charge_value'])
			->setDescription($data['description'])
			->setCustomersId($data['customer_id'])
			->setPaymentMethodId($data['payment_methods']);

		$success = $charge->update();
		$msg = $success ?
			'Cobrança alterada com sucesso' :
			'Falha ao alterada cobrança. Tente novamente';

		header('Location: ' . BASE_PATH . '?success=' . $success . '&msg=' . $msg);
		exit;
	}

	/**
	 * "Delete" a charge, changing it's "deleted" value to true
	 */
	public function paidAction()
	{
		$charge = new Charges();
		$success = $charge->pay(IDENTIFIER);
		$msg = $success ?
			'Cobrança marcada como paga' :
			'Falha ao marcar cobrança como paga. Tente novamente';

		header('Location: ' . BASE_PATH . '?success=' . $success . '&msg=' . $msg);
		exit;
	}

	/**
	 * "Delete" a charge, changing it's "deleted" value to true
	 */
	public function deleteAction()
	{
		$charge = new Charges();
		$success = $charge->delete(IDENTIFIER);
		$msg = $success ?
			'Cobrança removida com sucesso' :
			'Falha ao deletar cobrança. Tente novamente';

		header('Location: ' . BASE_PATH . '?success=' . $success . '&msg=' . $msg);
		exit;
	}

	/**
	 * Crontab action to verify if some charge passed the due date
	 * If true, sets it to "Expired"
	 */
	public function crontabAction()
	{
		// If it's 8AM
		if (date('H') == '8') {
			$charge = new Charges();

			/*
			 * List all the charges with "pending" status which due date 
			 * is higher than yesterday
			 */
			$charge_list = $charge->listAll(
				0,
				0,
				'',
				'',
				date('Y-m-d', strtotime('-1 day')),
				0,
				1
			);

			foreach ($charge_list as $expired_charge) {
				$charge->expire($expired_charge->id);
				echo 'A cobrança #' . $expired_charge->id . ' expirou<br>';
			}

			die('Todas as cobranças foram verificadas');
		} else {
			die('Essa tarefa só roda às 8h da manhã');
		}
	}
}

// Automatically instantiates
$controller = new ChargesController();
