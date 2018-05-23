<?php
namespace Able\IO\Abstractions;

use \Generator;
use \Able\Prototypes\IStringable;

interface IReader extends IStringable {

	/**
	 * @return Generator
	 */
	public function read(): Generator;

}
