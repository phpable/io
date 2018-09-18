<?php
namespace Able\IO\Abstractions;

use \Able\Prototypes\IIteratable;
use \Able\IO\Abstractions\ILocated;

interface IReader
	extends IIteratable, ILocated {

	/**
	 * @return \Generator
	 */
	public function read(): \Generator;

}
