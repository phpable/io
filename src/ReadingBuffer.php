<?php
namespace Able\IO;

use \Able\IO\File;
use \Able\IO\Path;

use \Able\IO\Abstractions\IReader;
use \Able\IO\Abstractions\ABuffer;

use \Able\Reglib\Regexp;

/**
 * @method ReadingBuffer process(callable $Handler)
 */
class ReadingBuffer extends ABuffer
	implements IReader {

	/**
	 * Buffer constructor.
	 * @param File $File
	 */
	public final function __construct(File $File) {
		$this->Buffer = $File->getContent();
	}

	/**
	 * @return \Generator
	 */
	public final function read(): \Generator {
		return (new Regexp('/[\n\r]/'))->split($this->Buffer);
	}
}
