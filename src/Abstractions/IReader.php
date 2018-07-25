<?php
namespace Able\IO\Abstractions;

use \Generator;
use \Able\Prototypes\IIteratable;

interface IReader extends IIteratable {

	/**
	 * @return Generator
	 */
	public function read(): Generator;

}
