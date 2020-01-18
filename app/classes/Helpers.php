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
		return date('Y-m-d', strtotime($date));
	}

	/**
	 * Converts to brazilian date format (DD/MM/YYYY)
	 * @param string date
	 * @return string
	 */
	public static function formatDate(string $date): string
	{
		return date('d/m/Y', strtotime($date));
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
}
