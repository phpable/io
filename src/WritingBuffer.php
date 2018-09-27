<?php
namespace Able\IO;

use \Able\IO\File;
use \Able\IO\Path;
use \Able\IO\ReadingBuffer;

use \Able\IO\Abstractions\IWriter;
use \Able\IO\Abstractions\ABuffer;
use \Able\IO\Abstractions\ISource;

use \Able\Reglib\Regexp;
use \Able\Helpers\Str;

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
	 * @const int
	 */
	public const WM_PREPEND = 0b0001;

	/**
	 * @param \Generator $Input
	 * @param int $mode
	 * @return void
	 */
	public final function write(\Generator $Input, int $mode = 0): void {
		foreach ($Input as $line){
			if (~$mode & self::WM_PREPEND) {
				$this->Buffer = $this->Buffer
					. Str::unbreak($line, 1) . "\n";
			} else {
				$this->Buffer = Str::unbreak($line)
					. "\n" . $this->Buffer;
			}
		}
	}

	/**
	 * @return string
	 */
	public final function getContent(): string {
		return $this->Buffer;
	}

	/**
	 * @param \Able\IO\File $File
	 * @throws \Exception
	 */
	public final function save(File $File): void {
		$File->rewrite($this->Buffer);
	}

	/**
	 * @return ReadingBuffer
	 */
	public final function toReadingBuffer(): ReadingBuffer {
		return new ReadingBuffer($this);
	}
}
