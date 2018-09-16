<?php
namespace Able\IO;

use \Able\IO\File;
use \Able\IO\Path;
use \Able\IO\ReadingBuffer;

use \Able\IO\Abstractions\IWriter;
use \Able\IO\Abstractions\ABuffer;
use \Able\IO\Abstractions\ISource;

use \Able\Reglib\Regexp;

/**
 * @method WritingBuffer process(callable $Handler)
 */
class WritingBuffer extends ABuffer
	implements IWriter, ISource {

	/**
	 * @param \Generator $Source
	 * @return WritingBuffer
	 */
	public final static function create(\Generator $Source){
		($Buffer = new static())->write($Source);
		return $Buffer;
	}

	/**
	 * @param \Generator $Input
	 * @return void
	 */
	public function write(\Generator $Input): void {
		foreach ($Input as $line){
			$this->Buffer .= rtrim($line) . "\n";
		}
	}

	/**
	 * @return string
	 */
	public final function getContent(): string {
		return $this->Buffer;
	}

	/**
	 * @return ReadingBuffer
	 */
	public final function toReadingBuffer(): ReadingBuffer {
		return new ReadingBuffer($this);
	}

}
