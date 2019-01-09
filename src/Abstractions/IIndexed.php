<?php
namespace Able\IO\Abstractions;

interface IIndexed {

	/**
	 * @const int
	 */
	public const DEFAULT_INDEX = 0;

	/**
	 * @return int
	 */
	public function getIndex(): int;
}
