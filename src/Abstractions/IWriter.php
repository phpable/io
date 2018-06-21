<?php
namespace Able\IO\Abstractions;

use \Generator;

interface IWriter {

	/**
	 * @param Generator $Input
	 * @return void
	 */
	public function write(Generator $Input): void;
}
