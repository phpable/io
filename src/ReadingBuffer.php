<?php
namespace Able\IO;

use \Able\IO\Abstractions\ILocated;

use \Able\IO\Abstractions\IReader;
use \Able\IO\Abstractions\ISource;
use \Able\IO\Abstractions\ABuffer;

use \Able\Reglib\Regex;

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
	 * @var int
	 */
	private $index = self::DEFAULT_INDEX;

	/**
	 * @return int
	 */
	public final function getIndex(): int {
		return $this->Index;
	}

	/**
	 * Buffer constructor.
	 * @param ISource $Source
	 */
	public function __construct(ISource $Source) {
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
	public function read(): \Generator {
		$this->index = 0;

		foreach (Regex::create('/(?:\r\n|\n|\r)/')->split($this->Buffer) as $line){
			yield ++$this->index => $line;
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
