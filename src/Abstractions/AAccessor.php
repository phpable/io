<?php
namespace Able\IO\Abstractions;

use \Able\Prototypes\TStringable;
use \Able\Prototypes\IStringable;

use \Able\IO\File;

abstract class AAccessor
	implements IStringable{

	use TStringable;

	/**
	 * @var File
	 */
	protected $File = null;

	/**
	 * Reader constructor.
	 * @param File $File
	 */
	public function __construct(File $File) {
		$this->File = $File;
	}

	/**
	 * @return string
	 */
	public final function toString(): string {
		return $this->File->toString();
	}
}
