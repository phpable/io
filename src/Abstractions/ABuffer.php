<?php
namespace Able\IO\Abstractions;

abstract class ABuffer {

	/**
	 * @var string
	 */
	protected $Buffer = null;

	/**
	 * @param callable $Handler
	 * @return ABuffer
	 */
	public final function process(callable $Handler): ABuffer {
		$this->Buffer = call_user_func($Handler, $this->Buffer);
		return $this;
	}

	/**
	 * @return string
	 */
	public final function getContent(): string {
		return $this->Buffer;
	}
}
