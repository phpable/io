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
	private $active = -1;

	/**
	 * @return \Generator
	 */
	public function read(): \Generator {
		foreach ($this->Collection as $this->active => $Reader){
			yield from $Reader->read();
		}

		$this->active = -1;
	}

	/**
	 * @return string
	 */
	public final function getLocation(): string {
		if (isset($this->Collection[$this->active])){
			return $this->Collection[$this->active]->getLocation();
		}

		return self::DEFAULT_LOCATION;
	}

	/**
	 * @return int
	 */
	public final function getIndex(): int {
		if (isset($this->Collection[$this->active])){
			return $this->Collection[$this->active]->getIndex();
		}

		return self::DEFAULT_INDEX;
	}

	/**
	 * @return \Generator
	 * @throws \Exception
	 */
	public final function iterate(): \Generator {
		return $this->read();
	}
}
