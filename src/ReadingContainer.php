<?php
namespace Able\IO;

use \Able\IO\Abstractions\IReader;
use \Able\IO\Abstractions\ABuffer;

use \Able\Prototypes\ICountable;

class ReadingContainer
	implements IReader, ICountable {

	/**
	 * @var IReader[]
	 */
	private $Collection = [];

	/**
	 * @return int
	 */
	public final function count(): int {
		return count($this->Collection);
	}

	/**
	 * @param IReader $Reader
	 * @return void
	 */
	public final function collect(IReader $Reader): void {
		array_push($this->Collection, $Reader);
	}

	/**
	 * @var int
	 */
	private $line = 0;

	/**
	 * @return int
	 */
	public final function getLine(): int {
		return $this->line;
	}

	/**
	 * @return \Generator
	 */
	public final function read(): \Generator {
		while(count($this->Collection) > 0){
			foreach ($this->Collection[0]->read() as $this->line => $line){
				yield $line;
			}

			array_shift($this->Collection);
		}
	}

	/**
	 * @return string
	 */
	public final function getLocation(): string {
		if (!empty($this->Collection)){
			return $this->Collection[0]->getLocation();
		}

		return self::DEFAULT_LOCATION;
	}

	/**
	 * @return \Generator
	 * @throws \Exception
	 */
	public final function iterate(): \Generator {
		return $this->read();
	}
}
