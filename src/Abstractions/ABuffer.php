<?php
namespace Able\IO\Abstractions;

abstract class ABuffer {

	/**
	 * @var string
	 */
	protected string $Buffer = '';

	/**
	 * @return int
	 */
	public final function getLength(): int {
		return strlen($this->Buffer);
	}

	/**
	 * @param callable $Handler
	 * @return ABuffer
	 */
	public final function process(callable $Handler): ABuffer {
		$this->Buffer = call_user_func($Handler, $this->Buffer);
		return $this;
	}

}
