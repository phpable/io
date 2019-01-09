<?php
namespace Able\IO\Abstractions;

use \Able\Prototypes\IIteratable;

use \Able\IO\Abstractions\ILocated;
use \Able\IO\Abstractions\IIndexed;

interface IReader
	extends IIteratable, ILocated, IIndexed {

	/**
	 * @return \Generator
	 */
	public function read(): \Generator;

}
