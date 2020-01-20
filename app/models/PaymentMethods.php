<?php

namespace Models;

use \Classes\DBConnect;

class PaymentMethods
{
	private $id;
	private $description;

	// Initialize the object
	public function __construct()
	{
		// Instantiate DB Connection
		$this->db = DBConnect::getInstance();
	}

	/**
	 * Get payment methods list to display on form
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function listAll(int $limit = 0, int $offset = 0): array
	{
		$query = '
			SELECT *
			FROM payment_methods
			ORDER BY id ASC';

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

	public function getDescription(): int
	{
		return $this->id;
	}
}
