<?php
namespace Able\IO\Abstractions;

use \Able\Prototypes\IStringable;
use \Able\Prototypes\TStringable;

use \Able\IO\Path;
use \Able\IO\Abstractions\IPatchable;

use \Exception;

abstract class ANode
	implements IStringable, IPatchable {

	use TStringable;

	/**
	 * @var Path
	 */
	private Path $Path;

	/**
	 * @return string
	 * @throws Exception
	 */
	protected final function assemble(): string {
		if (!$this->Path->isReadable()){
			throw new Exception('Given path is not exists or not readable: ' . $this->Path . '!');
		}

		return $this->Path->toString();
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public function toString(): string {
		return (string)$this->assemble();
	}

	/**
	 * @param Path $Path
	 * @throws Exception
	 */
	public function __construct(Path $Path) {
		if (!$Path->isReadable()) {
			throw new Exception('Given path is not exists or not readable: ' . $Path . '!');
		}

		$this->Path = $Path;
	}

	/**
	 * @param Path $Path
	 * @throws Exception
	 */
	protected function replacePath(Path $Path): void {
		if (!$Path->isExists()) {
			throw new Exception(sprintf('Given path is not exists or not readable: %s!', $Path));
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
	 * @throws Exception
	 */
	public final function getBaseName(): string {
		return basename($this->assemble());
	}

	/**
	 * @param IPatchable $Destination
	 * @param bool $rewrite
	 * @return void
	 */
	abstract public function copy(IPatchable $Destination, bool $rewrite = false): void;

	/**
	 * @return int
	 */
	abstract public function getSize(): int;

	/**
	 * @return  void
	 */
	abstract public function remove(): void;

	/**
	 * @param string $name
	 * @return void
	 */
	abstract public function rename(string $name): void;
}
