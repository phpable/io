<?php
namespace Able\IO;

use \Able\IO\File;
use \Able\IO\Path;

use \Able\IO\Abstractions\IReader;

use \Able\Reglib\Regexp;
use \Able\Prototypes\TStringable;

class Buffer implements IReader {
	use TStringable;

	/**
	 * @var Path
	 */
	private $Path = null;

	/**
	 * @var string
	 */
	private $Buffer = null;

	/**
	 * Buffer constructor.
	 * @param File $File
	 */
	public final function __construct(File $File) {
		$this->Path = $File->toPath();
		$this->Buffer = $File->getContent();
	}

	/**
	 * @return \Generator
	 */
	public final function read(): \Generator {
		return (new Regexp('/[\n\r]/'))->split($this->Buffer);
	}

	/**
	 * @return string
	 */
	public final function toString(): string {
		return $this->Path->toString();
	}
}
