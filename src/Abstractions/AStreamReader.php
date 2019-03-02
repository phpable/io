<?php
namespace Able\IO\Abstractions;

use \Able\IO\ReadingStream;

abstract class AStreamReader {

	/**
	 * @var ReadingStream
	 */
	private ReadingStream $Stream;

	/**
	 * @return ReadingStream
	 */
	protected final function stream(): ReadingStream {
		return $this->Stream;
	}

	/**
	 * @param ReadingStream $Stream
	 */
	public function __construct(ReadingStream $Stream) {
		$this->Stream = $Stream;
	}

}
