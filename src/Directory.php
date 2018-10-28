<?php
namespace Able\IO;

use \Able\Helpers\Fs;
use \Able\IO\Abstractions\ANode;

final class Directory extends ANode {

	/**
	 * @const int
	 */
	const AM_INHERIT = 0;

	/**
	 * @const int
	 */
	const AM_PUBLIC = 1;

	/**
	 * @param Path $Path
	 * @param int $mode
	 * @throws \Exception
	 */
	public final function __construct(Path $Path, int $mode = self::AM_INHERIT) {
		if (!in_array($mode, [self::AM_INHERIT, self::AM_PUBLIC])){
			throw new \Exception('Undefined mode!');
		}

		if ($Path->isFile()) {
			throw new \Exception('Path "' . $Path->toString() . '" is a file!');
		}

		if ($Path->isLink()) {
			throw new \Exception('Path "' . $Path->toString() . '" is a link!');
		}

		if (!$Path->isExists()) {
			if (!is_writable($root = Fs::ppath($Path->toString()))){
				throw new \Exception(sprintf('Path "%s" does not exists or not writable!', $Path->toString()));
			}

			mkdir($Path->toString(), $mode == self::AM_INHERIT
				? fileperms($root) : 0777, true);
		}

		parent::__construct($Path);
	}

	/**
	 * @return \Generator
	 * @throws \Exception
	 */
	public final function list(): \Generator {
		$h = opendir($this->assemble());

		try{
			while(($file = readdir($h)) !== false){
				yield new Path($this->assemble(), $file);
			}
		} finally {
			closedir($h);
		}
	}

	/**
	 * @param string $mask
	 * @return \Generator
	 * @throws \Exception
	 */
	public final function filter(string $mask): \Generator {
		foreach ($this->list() as $Path){
			if ($Path->isMatch($mask)){
				yield $Path;
			}
		}
	}

	/**
	 * @param string $mask
	 * @return \Generator
	 * @throws \Exception
	 */
	public final function glob(string $mask): \Generator {
		foreach (glob($this->toString() . '/' . ltrim($mask, '/'),
			GLOB_NOSORT | GLOB_BRACE) as $path){
				yield Path::create($path);
		}
	}

	/**
	 * @return bool
	 * @throws \Exception
	 */
	public final function isEmpty(): bool {
		foreach ($this->list() as $Path){
			if (!$Path->isDot()){
				return false;
			}
		}

		return true;
	}

	/**
	 * @return void
	 * @throws \Exception
	 */
	public final function clear(): void {
		foreach ($this->list() as $Path){
			if (!$Path->isDot()) {

				if ($Path->isLink()) {
					throw new \Exception('Cannot remove the link: ' . $Path->toString());
				}

				if ($Path->isDirectory()) {
					$Path->toDirectory()->remove();
				} else {
					$Path->toFile()->remove();
				}
			}
		}
	}

	/**
	 * @return void
	 * @throws \Exception
	 */
	public final function remove(): void {
		$this->clear();

		if (!@rmdir($this->assemble())){
			throw new \Exception('Cannot remove the directory: ' . $this->toString());
		}
	}

	/**
	 * @param Path $Destination
	 * @return void
	 * @throws \Exception
	 */
	public final function copy(Path $Destination): void {
		$this->clone($Destination->append($this->getBaseName())->try(function(){
			throw new \Exception('Destination is not a directory!');
		}, Path::TIF_FILE | Path::TIF_LINK)->forceDirectory());
	}

	/**
	 * @param Directory $Destination
	 * @return void
	 * @throws \Exception
	 */
	public final function clone(Directory $Destination): void {
		if (!$Destination->isEmpty()){
			throw new \Exception('Destination is not empty!');
		}

		foreach ($this->list() as $Path) {
			if (!$Path->isDot()){
				if ($Path->isDirectory()){
					$Path->toDirectory()->copy($Destination->toPath());
				} else{
					copy($Path, $Destination->toPath()->append($Path->getEnding()));
				}
			}
		}
	}
}
