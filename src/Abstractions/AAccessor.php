<?php
namespace Able\IO\Abstractions;

use \Able\Prototypes\TStringable;
use \Able\Prototypes\IStringable;

use \Able\IO\File;
use \Able\IO\Abstractions\ILocated;

use \Exception;

abstract class AAccessor
	implements IStringable, ILocated {

	use TStringable;

	/**
	 * @var File
	 */
	protected File $File;

	/**
	 * Reader constructor.
	 * @param File $File
	 */
	public function __construct(File $File) {
		$this->File = $File;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public final function toString(): string {
		return $this->File->toString();
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public final function getLocation(): string {
		return $this->File->getLocation();
	}
}
