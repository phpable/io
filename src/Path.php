<?php
namespace Able\IO;

use \Eggbe\Helper\Arr;
use \Eggbe\Helper\Str;

use \Able\Prototypes\IArrayable;
use \Able\Prototypes\ICountable;
use \Able\Prototypes\IStringable;
use \Able\Prototypes\TStringable;

use \Able\IO\Abstractions\APath;

use \Able\IO\File;
use \Able\IO\Directory;
use PHPUnit\Runner\Exception;

class Path extends APath implements IStringable, IArrayable, ICountable {
	use TStringable;

	/**
	 * @const string
	 */
	private const DS = DIRECTORY_SEPARATOR;

	/**
	 * @var array
	 */
	private $Fragments = [];

	/**
	 * @return int
	 */
	public final function count(): int {
		return count($this->Fragments);
	}

	/**
	 * @param $args, ...
	 * @throws \Exception
	 */
	public final function __construct($args = null){
		if (!is_null($args) || count(func_get_args()) > 0){
			$this->append(...func_get_args());
		}
	}

	/**
	 * @var string
	 */
	protected $point = null;

	/**
	 * @return bool
	 */
	public final function isAbsolute(): bool {
		return !is_null($this->point);
	}

	/**
	 * @param mixed $fragment, ...
	 * @return Path
	 * @throws \Exception
	 */
	public final function append($fragment) : Path {
		foreach(Arr::simplify(func_get_args()) as $fragment){
			if (count($this->Fragments) < 1 && is_null($this->point)) {
				$this->point = self::detectPoint($fragment);
			}

			$this->Fragments = Arr::append($this->Fragments, preg_split('/' . preg_quote(self::DS, '/')
				. '/', self::removePoint($fragment), -1, PREG_SPLIT_NO_EMPTY));
		}

		return $this;
	}

	/**
	 * @param mixed $fragment, ...
	 * @return Path
	 * @throws \Exception
	 */
	public final function prepend($fragment) : Path {
		foreach(Arr::simplify(func_get_args()) as $fragment){
			$this->point = self::detectPoint($fragment);

			$this->Fragments = Arr::prepend($this->Fragments, preg_split('/' . preg_quote(self::DS, '/') . '/',
				self::removePoint($fragment), -1, PREG_SPLIT_NO_EMPTY));
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public final function toString() : string {
		return implode(self::DS, Arr::unshift($this->Fragments,
			rtrim($this->point, self::DS)));
	}

	/**
	 * @return array
	 */
	public final function toArray() : array {
		return $this->Fragments;
	}

	/**
	 * @return Path
	 */
	public final function toPath() : Path {
		return clone $this;
	}

	/**
	 * @return Path
	 * @throws \Exception
	 */
	public final function getParent() : Path {
		return new Path(Arr::unshift(array_slice($this->Fragments,
			0, count($this->Fragments) - 1), $this->point));
	}

	/**
	 * @return Directory
	 * @throws \Exception
	 */
	public final function toDerectory() : Directory {
		if (!$this->isExists()){
			throw new \Exception(sprintf('Path "%s" does not exists!', $this->toString()));
		}

		return new Directory($this);
	}

	/**
	 * @return Directory
	 * @throws \Exception
	 */
	public final function forceDirectory(): Directory {
		return new Directory($this);
	}

	/**
	 * @return File
	 * @throws \Exception
	 */
	public final function toFile(): File {
		if (!$this->isExists()){
			throw new \Exception(sprintf('Path "%s" does not exists!', $this->toString()));
		}

		return new File($this);
	}

	/**
	 * @return File
	 * @throws \Exception
	 */
	public final function forceFile(): File {
		return new File($this);
	}

	/**
	 * Determines if a path exists.
	 *
	 * @return bool
	 */
	public final function isExists() : bool {
		return file_exists($this->toString());
	}

	/**
	 * @return bool
	 */
	public final function isDirectory() : bool {
		return $this->isExists() && is_dir($this->toString());
	}

	/**
	 * @return bool
	 */
	public final function isFile() : bool {
		return $this->isExists() && is_file($this->toString());
	}

	/**
	 * @return bool
	 */
	public final function isLink() : bool {
		return $this->isExists() && is_link($this->toString());
	}

	/**
	 * @return bool
	 */
	public final function isWritable() : bool {
		return $this->isExists() && is_writable($this->toString());
	}

	/**
	 * @return bool
	 */
	public final function isReadable() : bool {
		return $this->isExists() && is_readable($this->toString());
	}

	/**
	 * @return bool
	 */
	public final function isHidden() : bool {
		return preg_match('/^\.$/', $this->Fragments[count($this->Fragments ) - 1]) > 0;
	}
}
