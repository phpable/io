<?php
namespace Able\IO\Abstractions;

use \Generator;
use \Able\IO\File;

interface IWriter {

	/**
	 * @param Generator $Input
	 * @return void
	 */
	public function write(\Generator $Input): void;
}
