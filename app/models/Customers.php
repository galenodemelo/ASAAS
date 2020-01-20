<?php

namespace Models;

use \Classes\DBConnect;
use \Classes\Helpers;

class Customers
{
	private $id;
	private $name;
	private $email = '';
	private $cpf = '';
	private $deleted = false;
	private $created_at;

	// Initialize the object
	public function __construct()
	{
		// Instantiate DB Connection
		$this->db = DBConnect::getInstance();
	}

	/**
	 * Create a new customer
	 * @return bool
	 */
	public function create(): bool
	{
		$stmt = $this->db->prepare('
			INSERT INTO customers(
				name,
				email,
				cpf
			)
			VALUES (
				:name,
				:email,
				:cpf
			)');

		return $stmt->execute(array(
			':name' => $this->name,
			':email' => $this->email,
			':cpf' => $this->cpf
		));
	}

	/**
	 * Update customer data
	 * @return bool
	 */
	public function update(): bool
	{
		$stmt = $this->db->prepare('
			UPDATE customers
			SET name = :name,
				email = :email,
				cpf = :cpf
			WHERE id = :id');

		return $stmt->execute(array(
			':id' => $this->id,
			':name' => $this->name,
			':email' => $this->email,
			':cpf' => $this->cpf
		));
	}

	/**
	 * Find customer by id
	 * @param int $id
	 * @return bool
	 */
	public function findById(int $id): bool
	{
		$stmt = $this->db->prepare('
			SELECT *
			FROM customers
			WHERE id = :id
			LIMIT 1
		');
		$stmt->execute([
			':id' => $id
		]);

		// If there's a customer, pass it's values to the object
		if ($customer = $stmt->fetch()) {
			foreach ($customer as $key => $val) {
				$this->$key = $val;
			}
		}

		// If it's not empty, return true
		return !empty($customer);
	}

	/**
	 * Find customer by email or cpf
	 * @param string $id
	 * @return bool
	 */
	public function findByEmailCpf(string $email = null, string $cpf = null): bool
	{
		$stmt = $this->db->prepare('
			SELECT *
			FROM customers
			WHERE (email = :email AND email IS NOT NULL)
			   OR (cpf = :cpf AND cpf IS NOT NULL)
			LIMIT 1
		');
		$stmt->execute([
			':email' => $email,
			':cpf' => Helpers::getOnlyNumbers($cpf)
		]);

		// If there's a customer, pass it's values to the object
		if ($customer = $stmt->fetch()) {
			foreach ($customer as $key => $val) {
				$this->$key = $val;
			}
		}

		// If it's not empty, return true
		return !empty($customer);
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

	public function getName(): string
	{
		return $this->name;
	}

	public function getEmail(): string
	{
		return $this->email ?? '';
	}

	public function getCpf(bool $format = true): string
	{
		if ($format && !empty($this->cpf)) {
			return Helpers::formatCpf($this->cpf);
		} else {
			return $this->cpf ?? '';
		}
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

	/**
	 * *************************************
	 * SETTERS
	 * *************************************
	 */
	public function setId($id): Customers
	{
		$this->id = (int) $id;

		return $this;
	}

	public function setName($name): Customers
	{
		$this->name = Helpers::sanitizeString($name);

		return $this;
	}

	public function setEmail($email): Customers
	{
		if (Helpers::checkEmail($email)) {
			$this->email = $email;
		}

		return $this;
	}

	public function setCpf($cpf): Customers
	{
		if (Helpers::checkCpf($cpf)) {
			$this->cpf = Helpers::getOnlyNumbers($cpf);
		}

		return $this;
	}

	public function setDeleted($deleted): Customers
	{
		$this->deleted = (bool) $deleted;

		return $this;
	}
}
