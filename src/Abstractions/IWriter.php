<?php
namespace Able\IO\Abstractions;

use \Generator;
use \Able\IO\File;

use \Able\Prototypes\IStringable;

interface IWriter extends IStringable {

	/**
	 * @param Generator $Input
	 * @return void
	 */
	public function write(\Generator $Input): void;
}
