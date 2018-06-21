<?php
namespace Able\IO\Abstractions;

use \Generator;

interface IReader {

	/**
	 * @return Generator
	 */
	public function read(): Generator;

}
