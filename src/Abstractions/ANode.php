<?php
namespace Able\IO\Abstractions;

use \Able\Prototypes\IStringable;
use \Able\Prototypes\TStringable;

use \Able\IO\Path;

abstract class ANode implements IStringable {
	use TStringable;

	/**
	 * @var Path
	 */
	private $Path = null;

	/**
	 * @param Path $Path
	 * @throws \Exception
	 */
	public function __construct(Path $Path) {
		if (!$Path->isReadable()) {
			throw new \Exception('Path "' . (string)$Path . '" is not exists or not readable!');
		}

		$this->Path = $Path;
	}

	/**
	 * @return string
	 */
	public final function toString() : string {
		return (string)$this->Path->toString();
	}

	/**
	 * @return Path
	 */
	public final function toPath() : Path {
		return clone $this->Path->toPath();
	}

	/**
	 * @return string
	 */
	public final function getBaseName(): string {
		return basename((string)$this->Path);
	}

}
