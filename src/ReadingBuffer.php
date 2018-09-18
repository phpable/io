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
	 * @var null
	 */
	private $location = 'Unknown';

	/**
	 * @return string
	 */
	public final function getLocation(): string {
		return $this->location;
	}

	/**
	 * Buffer constructor.
	 * @param ISource $Source
	 */
	public final function __construct(ISource $Source) {
		$this->Buffer = $Source->getContent();

		/**
		 * If the given source implements the 'ILocated' interface,
		 * the source's location has to been copied to a Reader location.
		 */
		if ($Source instanceof ILocated){
			$this->location = $Source->getLocation();
		}
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
