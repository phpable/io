<?php
namespace Able\IO;

use \Able\IO\Abstractions\ILocated;

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
	private $location = self::DEFAULT_LOCATION;

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
	 * @param ISource $Source
	 * @return ReadingBuffer
	 */
	public final static function create(ISource $Source){
		return new static($Source);
	}

	/**
	 * @return \Generator
	 * @throws \Exception
	 */
	public final function read(): \Generator {
		foreach (Regexp::create('/(?:\r\n|\n|\r)/')->split($this->Buffer) as $index => $line){
			yield ++$index => $line;
		}
	}

	/**
	 * @return \Generator
	 * @throws \Exception
	 */
	public final function iterate(): \Generator {
		return $this->read();
	}

}
