<?php

namespace Models;

use \Classes\DBConnect;
use \Classes\Helpers;

class Charges
{
	private $id;
	private $due_date;
	private $payment_date = null;
	private $value;
	private $description;
	private $deleted = false;
	private $created_at;
	private $companies_id = 1;
	private $customers_id;
	private $payment_methods_id;
	// Status id 1 is equal to "pending"
	private $charges_status_id = 1;
	private $number_results;

	// Initialize the object
	public function __construct($charge_data = null)
	{
		// Instantiate DB Connection
		$this->db = DBConnect::getInstance();
	}

	/**
	 * Create a new charge
	 * @return Bool
	 */
	public function create(): bool
	{
		$stmt = $this->db->prepare('
			INSERT INTO charges(
				due_date,
				payment_date,
				value,
				description,
				companies_id, 
				customers_id,
				payment_methods_id,
				charges_status_id
			)
			VALUES (
				:due_date,
				:payment_date,
				:value,
				:description,
				:companies_id,
				:customers_id,
				:payment_methods_id,
				:charges_status_id
			)');

		return $stmt->execute(array(
			':due_date' => $this->due_date,
			':payment_date' => $this->payment_date,
			':value' => $this->value,
			':description' => $this->description,
			':companies_id' => $this->companies_id,
			':customers_id' => $this->customers_id,
			':payment_methods_id' => $this->payment_methods_id,
			':charges_status_id' => $this->charges_status_id
		));
	}

	/**
	 * Update the current charge
	 * @return Bool
	 */
	public function update(): bool
	{
		$stmt = $this->db->prepare('
			UPDATE charges
			SET due_date = :due_date, 
				payment_date = :payment_date,
				value = :value, 
				description = :description,
				companies_id = :companies_id,
				customers_id = :customers_id,
				payment_methods_id = :payment_methods_id,
				charges_status_id = :charges_status_id
			WHERE id = :id
		');

		return $stmt->execute([
			':due_date' => $this->due_date,
			':payment_date' => $this->payment_date,
			':value' => $this->value,
			':description' => $this->description,
			':companies_id' => $this->companies_id,
			':customers_id' => $this->customers_id,
			':payment_methods_id' => $this->payment_methods_id,
			':charges_status_id' => $this->charges_status_id,
			':id' => $this->id
		]);
	}

	/**
	 * Mark a charge as paid today and changing it's status
	 * @param int $id
	 * @return bool
	 */
	public function pay(int $id): bool
	{
		$stmt = $this->db->prepare('
			UPDATE charges
			SET payment_date = :payment_date,
				charges_status_id = :charges_status_id
			WHERE id = :id
		');
		return $stmt->execute([
			':id' => $id,
			':payment_date' => date('Y-m-d'),
			':charges_status_id' => 2
		]);
	}

	/**
	 * Set a charge as expired
	 * @param int $id
	 * @return bool
	 */
	public function expire(int $id): bool
	{
		$stmt = $this->db->prepare('
			UPDATE charges
			SET charges_status_id = :charges_status_id
			WHERE id = :id
		');
		return $stmt->execute([
			':id' => $id,
			':charges_status_id' => 3
		]);
	}

	/**
	 * "Delete" a charge, changing it's "deleted" value to true
	 * @param int $id
	 */
	public function delete(int $id): bool
	{
		$stmt = $this->db->prepare('UPDATE charges SET deleted = :deleted WHERE id = :id');
		return $stmt->execute([
			':id' => $id,
			':deleted' => true
		]);
	}

	/**
	 * Get all the charges
	 * @param mixed $id
	 * @param bool
	 */
	public function findById($id): bool
	{
		$stmt = $this->db->prepare('
			SELECT *
			FROM charges
			WHERE id = :id
			  AND deleted != true
			LIMIT 1
		');
		$stmt->execute([
			':id' => (int) $id
		]);

		// If there's a charge, pass it's values to the object
		if ($charge = $stmt->fetch()) {
			foreach ($charge as $key => $val) {
				$this->$key = $val;
			}
		}

		// If it's not empty, return true
		return !empty($charge);
	}

	/**
	 * Get charges list to display on front-end
	 * @param int $limit
	 * @param int $offset
	 * @param string $name_email
	 * @param string $initial_date
	 * @param string $final_date
	 * @param string $payment_method
	 * @param string $charge_status
	 * @return array
	 */
	public function listAll(
		int $limit = RESULTS_PER_PAGE,
		int $offset = 0,
		string $name_email = '',
		string $initial_date = '',
		string $final_date = '',
		int $payment_method = 0,
		int $charge_status = 0
	): array {
		$query = '
			SELECT cha.id,
				cus.name AS customer,
				cha_sta.description AS status,
				cha.due_date,
				cha.description,
				pay.description AS payment_method,
				cha.payment_date,
				cha.value
			FROM charges cha
			INNER JOIN customers cus ON cus.id = cha.customers_id
			INNER JOIN charges_status cha_sta ON cha_sta.id = cha.charges_status_id
			INNER JOIN payment_methods pay ON pay.id = cha.payment_methods_id
			WHERE cha.deleted != true';

		// Filter by name/email
		if (!empty($name_email)) {
			$name_email = '%' . $name_email . '%';

			$query .= ' AND (LOWER(cus.name) LIKE LOWER("' . $name_email . '")
						OR LOWER(cus.email) LIKE LOWER("' . $name_email . '"))';
		}

		// Filter by initial and final date
		if (!empty($initial_date) && !empty($final_date)) {
			$query .= ' AND cha.due_date BETWEEN "' . $initial_date . '" AND "' . $final_date . '"';
		} else if (!empty($initial_date)) {
			$query .= ' AND cha.due_date >= "' . $initial_date . '"';
		} else if (!empty($final_date)) {
			$query .= ' AND cha.due_date <= "' . $final_date . '"';
		}

		// Filter by payment method
		if (!empty($payment_method)) {
			$query .= ' AND cha.payment_methods_id = ' . $payment_method;
		}

		// Filter by payment method
		if (!empty($charge_status)) {
			$query .= ' AND cha.charges_status_id = ' . $charge_status;
		}

		$query .= ' ORDER BY status DESC, due_date ASC';

		// Count the number of results
		$this->countResults($query);

		if (!empty($limit) && is_int($limit)) {
			$query .= ' LIMIT ' . $limit;
		}

		if (!empty($offset) && is_int($offset)) {
			$query .= ' OFFSET ' . $offset;
		}

		$stmt = $this->db->query($query);
		return $stmt->fetchAll();
	}

	/**
	 * Calculates de total of results
	 * @param string $query
	 * @return int
	 */
	public function countResults(string $query): int
	{
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$this->number_results = $stmt->rowCount();

		return $this->number_results;
	}

	/**
	 * *************************************
	 * GETTERS
	 * *************************************
	 */
	public function getId(): int
	{
		return $this->id;
	}

	public function getDueDate(bool $format = true): string
	{
		return $format ?
			Helpers::formatDate($this->due_date) :
			$this->due_date;
	}

	public function getPaymentDate(bool $format = true): string
	{
		return $format ?
			Helpers::formatDate($this->payment_date) :
			$this->due_date;
	}

	public function getValue(bool $format = true): string
	{
		return $format ?
			Helpers::formatMoney($this->value) :
			$this->value;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function getDeleted(): bool
	{
		return $this->deleted;
	}

	public function getCreatedAt(bool $format = true): string
	{
		return $format ?
			Helpers::formatDate($this->created_at) :
			$this->created_at;
	}

	public function getCompaniesId(): int
	{
		return $this->companies_id;
	}

	public function getCustomersId(): int
	{
		return $this->customers_id;
	}

	public function getPaymentMethodId(): int
	{
		return $this->payment_methods_id;
	}

	public function getChargesStatusId(): int
	{
		return $this->charges_status_id;
	}

	public function getNumberResults(): int
	{
		return $this->number_results;
	}

	/**
	 * *************************************
	 * SETTERS
	 * *************************************
	 */

	/**
	 * @param int $id
	 * @return Charges
	 */
	public function setId($id): Charges
	{
		$this->id = (int) $id;

		return $this;
	}

	/**
	 * @param string $date
	 * @return Charges
	 */
	public function setDueDate($date): Charges
	{
		// Converts to DB date format
		$this->due_date = Helpers::dbDateFormat($date);

		return $this;
	}

	/**
	 * @param string date
	 * @return Charges
	 */
	public function setPaymentDate($date): Charges
	{
		// Converts to DB date format
		$this->payment_date = Helpers::dbDateFormat($date);

		return $this;
	}

	/**
	 * @param string $description
	 * @return Charges
	 */
	public function setValue($value): Charges
	{
		$this->value = Helpers::dbFormatMoney($value);

		return $this;
	}

	/**
	 * @param string $description
	 * @return Charges
	 */
	public function setDescription($description): Charges
	{
		$this->description = Helpers::sanitizestring($description);

		return $this;
	}

	/**
	 * @param Boolean $deleted
	 * @return Charges
	 */
	public function setDeleted($deleted): Charges
	{
		$this->deleted = (bool) $deleted;

		return $this;
	}

	/**
	 * @param int $id
	 * @return Charges
	 */
	public function setCompaniesId($id): Charges
	{
		$this->companies_id = (int) $id;

		return $this;
	}

	/**
	 * @param int $id
	 * @return Charges
	 */
	public function setCustomersId($id): Charges
	{
		$this->customers_id = (int) $id;

		return $this;
	}

	/**
	 * @param int $id
	 * @return Charges
	 */
	public function setPaymentMethodId($id): Charges
	{
		$this->payment_methods_id = (int) $id;

		return $this;
	}

	/**
	 * @param int $id
	 * @return Charges
	 */
	public function setChargesStatusId($id): Charges
	{
		$this->charges_status_id = (int) $id;

		return $this;
	}
}
