<?php
namespace Able\IO\Abstractions;

interface IBehavior {

	/**
	 * @param string $fragment
	 * @return string|null
	 */
	public static function detectPoint(string $fragment): ?string;

	/**
	 * @param string $fragment
	 * @return string
	 */
	public static function removePoint(string $fragment): string;
}
