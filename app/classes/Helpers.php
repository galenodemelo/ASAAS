<?php

namespace Classes;

class Helpers
{
	/**
	 * Converts to database date format (YYYY-MM-DD)
	 * @param string date
	 * @return string
	 */
	public static function dbDateFormat(string $date): string
	{
		$date = str_replace('/', '-', $date);
		return strtotime($date) ?
			date('Y-m-d', strtotime($date)) :
			'';
	}

	/**
	 * Converts to brazilian date format (DD/MM/YYYY)
	 * @param string date
	 * @return string
	 */
	public static function formatDate($date): string
	{
		return ($timestamp = strtotime($date)) ?
			date('d/m/Y', $timestamp) :
			'-';
	}

	/**
	 * Converts brazilian money format to database float format
	 * (two decimals separated by dot, thousands without separator) 
	 * @param string $value
	 * @return float
	 */
	public static function dbFormatMoney(string $value): float
	{
		// Remove the dots thousands separator
		$valueWithoutDots = str_replace('.', '', $value);

		// Change the comma thousands separator to commas
		$valueWithDotDecimal = str_replace(',', '.', $valueWithoutDots);

		// Return as float
		return number_format($valueWithDotDecimal, 2, '.', '');
	}

	/**
	 * Converts to brazilian money format
	 * (two decimals separated by comma, thousands separated by dot) 
	 * @param float $value
	 * @return string
	 */
	public static function formatMoney(float $value): string
	{
		return number_format($value, 2, ',', '.');
	}

	/**
	 * Sanitizes the string to remove HTML and special tags
	 * @param string string
	 * @return string
	 */
	public static function sanitizeString(string $string): string
	{
		return filter_var($string, FILTER_SANITIZE_STRING);
	}

	/**
	 * Check if email is valid returning a verification
	 * If it keeps the value after the filter, then it's a valid email
	 * @param string email
	 * @return bool
	 */
	public static function checkEmail(string $email): bool
	{
		return !empty(filter_var($email, FILTER_SANITIZE_EMAIL));
	}

	/**
	 * Remove all non-numeric characters
	 * @param string $number
	 * @return string
	 */
	public static function getOnlyNumbers(string $number): string
	{
		return preg_replace('/[^0-9]/is', '', $number);
	}

	/**
	 * Check if CPF is valid
	 * @param string email
	 * @return bool
	 * @author Rafael Neri <https://gist.github.com/rafael-neri/ab3e58803a08cb4def059fce4e3c0e40>
	 */
	public static function checkCpf(string $cpf): bool
	{
		// Number only extraction
		$cpf = self::getOnlyNumbers($cpf);

		// Check if it has 11 characters
		if (strlen($cpf) != 11) {
			return false;
		}

		// Calculates the verification digit
		for ($t = 9; $t < 11; $t++) {
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf{
					$c} * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($cpf{
				$c} != $d) {
				return false;
			}
		}

		return $cpf;
	}

	/**
	 * Format CPF on "XXX.XXX.XXX-XX" pattern
	 * @param string $cpf
	 * @return string
	 */
	public static function formatCpf(string $cpf): string
	{
		return substr($cpf, 0, 3) . '.'
			. substr($cpf, 3, 3) . '.'
			. substr($cpf, 6, 3) . '-'
			. substr($cpf, 9);
	}

	/**
	 * Get sended form data and sanitizes it
	 * @param array $fields
	 * @param int $input_type
	 * @return array
	 */
	public static function getFormData(array $fields, int $input_type = INPUT_POST): array
	{
		$form_data = array();

		// Iterates over each field and sanitize it
		foreach ($fields as $field) {
			$form_data[$field] = trim(filter_input($input_type, $field, FILTER_SANITIZE_STRING)) ?? '';
		}

		return $form_data;
	}

	/**
	 * Count the number of pages
	 * @param int $total_resutls
	 * @param int $results_per_page
	 * @return int
	 */
	public static function countPages(int $total_results, int $results_per_page = RESULTS_PER_PAGE): int
	{
		return ceil($total_results / $results_per_page);
	}

	/**
	 * Returns the current URL
	 */
	public static function getThisUrl(): string
	{
		$url = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
		$url .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		return $url;
	}
}
