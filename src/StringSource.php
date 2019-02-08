<?php
namespace Able\IO;

use \Able\IO\Abstractions\ISource;
use \Able\IO\ReadingBuffer;

class StringSource
	implements ISource {

	/**
	 * @var string
	 */
	private string $content = '';

	/**
	 * @param string $content
	 */
	public function __construct(string $content) {
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public final function getContent(): string {
		return $this->content;
	}

	/**
	 * @return ReadingBuffer
	 */
	public final function toReadingBuffer(): ReadingBuffer {
		return new ReadingBuffer($this);
	}
}
