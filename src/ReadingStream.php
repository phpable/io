<?php
namespace Able\IO;

use \Able\IO\File;
use \Able\IO\Abstractions\AStream;

use \Able\Helpers\Str;

class ReadingStream {

	/**
	 * @var resource
	 */
	private $handler;

	/**
	 * Buffer constructor.
	 * @param File $File
	 */
	public function __construct(File $File) {
		if (!is_resource($this->handler = fopen($File, 'r'))){
			throw new Exception('Invalid source!');
		}
	}

	/**
	 * Closes the file and release file handler.
	 */
	public function __destruct() {
		if (is_resource($this->handler)) {
			fclose($this->handler);
		}
	}

	/**
	 * @param File $File
	 * @return ReadingStream
	 */
	public final static function create(File $File){
		return new static($File);
	}

	/**
	 * @var int
	 */
	private int $index = 0;

	/**
	 * @return int
	 */
	public final function getIndex(): int {
		return $this->index;
	}

	/**
	 * @var int
	 */
	private int $position = 0;

	/**
	 * @return string|null
	 */
	public final function read(): ?string {
		$this->position = ftell($this->handler);

		if (($line = fgets($this->handler)) !== false) {
			$this->index++;

			return str::unbreak($line);
		}

		return null;
	}

	/**
	 * @return void
	 */
	public final function rollback(): void {
		fseek($this->handler, $this->position);
	}
}
