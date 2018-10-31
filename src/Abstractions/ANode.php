<?php
namespace Able\IO\Abstractions;

use \Able\Prototypes\IStringable;
use \Able\Prototypes\TStringable;

use \Able\IO\Path;
use \Able\IO\Abstractions\IPatchable;

abstract class ANode implements IStringable {
	use TStringable;

	/**
	 * @var Path
	 */
	private $Path = null;

	/**
	 * @return string
	 * @throws \Exception
	 */
	protected final function assemble(): string {
		if (!$this->Path->isReadable()){
			throw new \Exception('Given path is not exists or not readable: ' . $this->Path . '!');
		}

		return $this->Path->toString();
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public final function toString(): string {
		return (string)$this->assemble();
	}

	/**
	 * @param Path $Path
	 * @throws \Exception
	 */
	public function __construct(Path $Path) {
		if (!$Path->isReadable()) {
			throw new \Exception('Given path is not exists or not readable: ' . $Path . '!');
		}

		$this->Path = $Path;
	}

	/**
	 * @return Path
	 */
	public final function toPath(): Path {
		return clone $this->Path->toPath();
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public final function getBaseName(): string {
		return basename($this->assemble());
	}

	/**
	 * @param IPatchable $Destination
	 */
	abstract function copy(IPatchable $Destination): void;

}
