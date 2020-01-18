<?php

namespace Models;

use \Classes\DBConnect;
use \Classes\Helpers;

class Charges
{
	private $db;
	private $id;
	private $due_date;
	private $payment_date = null;
	private $value;
	private $description;
	private $deleted = false;
	private $companies_id;
	private $customers_id;
	private $payment_methods_id;
	// Status id 1 is equal to "Pendente"
	private $charges_status_id = 1;

	// Initialize the object
	public function __construct()
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
				"due_date",
				"payment_date",
				"value",
				"description",
				"companies_id", 
				"customers_id",
				"payment_methods_id",
				"charges_status_id"
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

		return $stmt->execute([
			':due_date' => $this->due_date,
			':payment_date' => $this->payment_date,
			':value' => $this->value,
			':description' => $this->description,
			':deleted' => $this->deleted,
			':companies_id' => $this->companies_id,
			':customers_id' => $this->customers_id,
			':payment_methods_id' => $this->payment_methods_id,
			':charges_status_id' => $this->charges_status_id
		]);
	}

	/**
	 * Update the current charge
	 * @return Bool
	 */
	public function update(): bool
	{
		$stmt = $this->db->prepare('
			UPDATE charges
			SET
				due_date = :due_date, 
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
			':id' => $this->id,
			':due_date' => $this->due_date,
			':payment_date' => $this->payment_date,
			':value' => $this->value,
			':description' => $this->description,
			':deleted' => $this->deleted,
			':companies_id' => $this->companies_id,
			':customers_id' => $this->customers_id,
			':payment_methods_id' => $this->payment_methods_id,
			':charges_status_id' => $this->charges_status_id
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
	 * Get all the charges that aren't deleted
	 * @param int $limit
	 * @param int $offset
	 * @return Array
	 */
	public function getAll(int $limit = 0, int $offset = 0): array
	{
		$query = '
			SELECT *
			FROM charges
			WHERE deleted != true';

		if (!empty($limit) && is_int($limit)) {
			$query .= ' LIMIT ' . $limit;
		}

		if (!empty($offset) && is_int($limit)) {
			$query .= ' OFFSET ' . $offset;
		}

		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll();
	}

	/**
	 * Get all the charges
	 * @param int $id
	 * @return Array
	 */
	public function getById(int $id)
	{
		$stmt = $this->db->prepare('
			SELECT *
			FROM charges
			WHERE id = :id
			LIMIT 1
		');
		$stmt->execute([
			':id' => $id
		]);

		// If there's a charge, pass it's values to the object
		if ($charge = $stmt->fetch()) {
			foreach ($charge as $key => $val) {
				$this->$key = $val;
			}
		}

		return $charge ?? false;
	}

	/**
	 * Get charges list to display on front-end
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getChargesList(int $limit = 0, int $offset = 0): array
	{
		$query = '
			SELECT
				cha.id,
				cus.name AS customer,
				cha_sta.description AS status,
				cha.due_date,
				cha.description,
				cha.value
			FROM charges cha
			INNER JOIN customers cus ON cus.id = cha.customers_id
			INNER JOIN charges_status cha_sta ON cha_sta.id = cha.charges_status_id
			WHERE cha.deleted != true';

		if (!empty($limit) && is_int($limit)) {
			$query .= ' LIMIT ' . $limit;
		}

		if (!empty($offset) && is_int($limit)) {
			$query .= ' OFFSET ' . $offset;
		}

		$stmt = $this->db->prepare($query);
		$stmt->execute();

		return $stmt->fetchAll();
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

	public function getDueDate(): string
	{
		return Helpers::formatDate($this->due_date);
	}

	public function getPaymentDate(): string
	{
		return Helpers::formatDate($this->payment_date);
	}

	public function getValue(): string
	{
		return Helpers::formatMoney($this->value);
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function getDeleted(): bool
	{
		return $this->deleted;
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

	/**
	 * *************************************
	 * SETTERS
	 * *************************************
	 */

	/**
	 * @param int $id
	 * @return Charges
	 */
	public function setId(int $id): Charges
	{
		$this->id = $id;

		return $this;
	}

	/**
	 * @param string $date
	 * @return Charges
	 */
	public function setDueDate(string $date): Charges
	{
		// Converts to DB date format
		$this->due_date = Helpers::dbDateFormat($date);

		return $this;
	}

	/**
	 * @param string date
	 * @return Charges
	 */
	public function setPaymentDate(string $date): Charges
	{
		// Converts to DB date format
		$this->payment_date = Helpers::dbDateFormat($date);

		return $this;
	}

	/**
	 * @param string $description
	 * @return Charges
	 */
	public function setDescription(string $description): Charges
	{
		$this->description = Helpers::sanitizestring($description);

		return $this;
	}

	/**
	 * @param Boolean $deleted
	 * @return Charges
	 */
	public function setDeleted(bool $deleted): Charges
	{
		$this->deleted = $deleted;

		return $this;
	}

	/**
	 * @param int $id
	 * @return Charges
	 */
	public function setCompaniesId(int $id): Charges
	{
		$this->companies_id = $id;

		return $this;
	}

	/**
	 * @param int $id
	 * @return Charges
	 */
	public function setCustomersId(int $id): Charges
	{
		$this->customers_id = $id;

		return $this;
	}

	/**
	 * @param int $id
	 * @return Charges
	 */
	public function setPaymentMethodId(int $id): Charges
	{
		$this->payment_methods_id = $id;

		return $this;
	}

	/**
	 * @param int $id
	 * @return Charges
	 */
	public function setChargesStatusId(int $id): Charges
	{
		$this->charges_status_id = $id;

		return $this;
	}
}
