<?php
namespace Able\IO;

use \Able\IO\File;
use \Able\IO\Path;

use \Able\IO\Abstractions\IReader;
use \Able\IO\Abstractions\ISource;
use \Able\IO\Abstractions\ABuffer;

use \Able\Reglib\Regexp;

/**
 * @method ReadingBuffer process(callable $Handler)
 */
class ReadingBuffer extends ABuffer
	implements IReader {

	/**
	 * Buffer constructor.
	 * @param ISource $Source
	 */
	public final function __construct(ISource $Source) {
		$this->Buffer = $Source->getContent();
	}

	/**
	 * @return \Generator
	 * @throws \Exception
	 */
	public final function read(): \Generator {
		return (new Regexp('/[\n\r]/'))->split($this->Buffer);
	}

	/**
	 * @return \Generator
	 * @throws \Exception
	 */
	public final function iterate(): \Generator {
		return $this->read();
	}

}
