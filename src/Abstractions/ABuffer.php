<?php
namespace Able\IO\Abstractions;

abstract class ABuffer {

	/**
	 * @var string
	 */
	protected $Buffer = '';

	/**
	 * @param callable $Handler
	 * @return ABuffer
	 */
	public final function process(callable $Handler): ABuffer {
		$this->Buffer = call_user_func($Handler, $this->Buffer);
		return $this;
	}

}
