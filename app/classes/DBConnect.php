<?php

/**
 * DB connection using PDO (PHP Database Object)
 */

namespace Classes;

use \PDO;
use \PDOException;

class DBConnect
{
	private const HOST = 'localhost';
	private const DBNAME = 'xl3r7tru__asaas';
	private const USER = 'root';
	private const PASS = '';
	private const OPTIONS = [
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		PDO::ATTR_EMULATE_PREPARES   => false,
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
		PDO::ATTR_CASE               => PDO::CASE_NATURAL
	];
	private static $instance;

	/**
	 * Gets database object to manipulate SQL queries
	 * @return PDO Object with all PDO methods
	 */
	public static function getInstance(): PDO
	{
		// If it isn't connected yet
		if (empty(self::$instance)) :

			try {
				self::$instance = new PDO(
					'mysql:host=' . self::HOST . ';dbname=' . self::DBNAME,
					self::USER,
					self::PASS,
					self::OPTIONS
				);
			} catch (PDOException $exception) {
				error_log($exception->getMessage(), 0);
				die('<h1>Erro ao conectar com o banco de dados</h1>');
			}
		endif;

		return self::$instance;
	}

	/**
	 * Disconnects from database
	 */
	public static function disconnect()
	{
		self::$instance = null;
	}

	/**
	 * Gets last ID of insert
	 * @return Int last id
	 */
	public static function getLastId(): int
	{
		return self::$instance->lastInsertId();
	}
}
