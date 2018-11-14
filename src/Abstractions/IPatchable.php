<?php
namespace Able\IO\Abstractions;

use \Able\IO\Path;

interface IPatchable {

	/**
	 * Converts any object to a path.
	 * @return Path
	 */
	public function toPath(): Path;
}
