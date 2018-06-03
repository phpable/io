<?php
namespace Able\IO;

use \Able\IO\File;
use \Able\IO\Path;
use \Able\IO\Abstractions\IReader;
use \Able\IO\Abstractions\AAccessor;

use \Able\Reglib\Regexp;

class Buffer extends AAccessor implements IReader {

	/**
	 * @var string
	 */
	private $Buffer = null;

	/**
	 * Buffer constructor.
	 * @param File $File
	 */
	public final function __construct(File $File) {
		$this->Buffer = $File->getContent();

		parent::__construct($File);
	}

	/**
	 * @param \Closure $Handler
	 * @return Buffer
	 */
	public final function process(\Closure $Handler): Buffer {
		$this->Buffer = $Handler($this->Buffer);
		return $this;
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
	public final function getContent(): string{
		return $this->Buffer;
	}
}
