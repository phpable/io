<?php
namespace Able\IO\Abstractions;

use \Able\IO\Abstractions\ISource;

abstract class ABuffer
	implements ISource {

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
