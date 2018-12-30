<?php
namespace Able\IO\Abstractions;

interface ILocated {

	/**
	 * @const string
	 */
	public const DEFAULT_LOCATION = 'Undefined';

	/**
	 * @return string
	 */
	public function getLocation(): string;
}
