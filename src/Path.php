<?php
namespace Able\IO;

use \Able\Helpers\Arr;
use \Able\Helpers\Str;

use \Able\Prototypes\IArrayable;
use \Able\Prototypes\ICountable;
use \Able\Prototypes\IStringable;
use \Able\Prototypes\TStringable;

use \Able\IO\Abstractions\APath;

use \Able\IO\File;
use \Able\IO\Directory;
use \Able\IO\Abstractions\ANode;
use \Exception;

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
	 * @throws Exception
	 */
	public final function __construct($args = null) {
		if (!is_null($args) || count(func_get_args()) > 0){
			$this->append(...func_get_args());
		}
	}

	/**
	 * @param $args, ...
	 * @return Path
	 * @throws Exception
	 */
	public static function create($args = null): Path {
		return new static(...func_get_args());
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
	 * @throws Exception
	 */
	public final function append($fragment) : Path {
		foreach(array_filter(Arr::simplify(func_get_args())) as $fragment){
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
	 * @throws Exception
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
	 * @param Path $Path
	 * @return Path
	 */
	public final function exclude(Path $Path): Path {
		if ($this->isChildOf($Path)) {
			foreach ($Path->toArray() as $fragment){
				if (count($this->Fragments) < 1 || $this->Fragments[0] !== $fragment){
					break;
				}

				array_shift($this->Fragments);
			}
		}

		return $this;
	}

	/**
	 * @return Path
	 * @throws Exception
	 */
	public final function getParent() : Path {
		return new Path(Arr::unshift(array_slice($this->Fragments,
			0, count($this->Fragments) - 1), $this->point));
	}

	/**
	 * @return string
	 */
	public final function getEnding(): string {
		return Arr::last($this->Fragments, $this->point);
	}

	/**
	 * @param string $ending
	 * @return Path
	 */
	public final function changeEnding(string $ending): Path {
		array_pop($this->Fragments);
		array_push($this->Fragments, $ending);

		return $this;
	}

	/**
	 * @return Directory
	 * @throws Exception
	 */
	public final function toDirectory() : Directory {
		if (!$this->isExists()){
			throw new Exception(sprintf('Path "%s" does not exists!', $this->toString()));
		}

		return new Directory($this);
	}

	/**
	 * @return Directory
	 * @throws Exception
	 */
	public final function forceDirectory(): Directory {
		return new Directory($this);
	}

	/**
	 * @return File
	 * @throws Exception
	 */
	public final function toFile(): File {
		if (!$this->isExists()){
			throw new Exception(sprintf('Path "%s" does not exists!', $this->toString()));
		}

		return new File($this);
	}

	/**
	 * @return File
	 * @throws Exception
	 */
	public final function forceFile(): File {
		return new File($this);
	}

	/**
	 * @return ANode
	 * @throws Exception
	 */
	public final function toNode(): ANode {
		if ($this->isDirectory()){
			return $this->toDirectory();
		}

		return $this->toFile();
	}

	/**
	 * @return string
	 */
	public final function toString() : string {
		return (!is_null($this->point) ? (rtrim($this->point, self::DS)
			. self::DS) : '') . Str::join(self::DS, $this->Fragments);
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
	 * Checks if the path matches a mask.
	 * @param string $mask
	 * @return bool
	 */
	public final function isMatch(string $mask): bool {
		return fnmatch($mask, $this->toString(), FNM_PATHNAME);
	}

	/**
	 * Checks if the path exists.
	 * @return bool
	 */
	public final function isExists() : bool {
		return file_exists($this->toString());
	}

	/**
	 * Checks if the path is a directory.
	 * @return bool
	 */
	public final function isDirectory() : bool {
		return $this->isExists() && is_dir($this->toString());
	}

	/**
	 * Determines if the path is a regular file.
	 * @return bool
	 */
	public final function isFile() : bool {
		return $this->isExists() && is_file($this->toString());
	}

	/**
	 * Determines if the current path is a symbolic link.
	 * @return bool
	 */
	public final function isLink() : bool {
		return $this->isExists() && is_link($this->toString());
	}

	/**
	 * Determines if the current path is writable.
	 * @return bool
	 */
	public final function isWritable() : bool {
		return $this->isExists() && is_writable($this->toString());
	}

	/**
	 * Determines if the current path is readable.
	 * @return bool
	 */
	public final function isReadable() : bool {
		return $this->isExists() && is_readable($this->toString());
	}

	/**
	 * Determines if current path is '.' or '..'.
	 * @return bool
	 */
	public final function isDot() : bool {
		return preg_match('/^\.{1,2}$/', $this->getEnding()) > 0;
	}

	/**
	 * Determines if the right fragment of the current path is starting from the dot character.
	 * @return bool
	 */
	public final function isHidden() : bool {
		return preg_match('/^\./', $this->getEnding()) > 0;
	}

	/**
	 * Determines if the current path is a child of another one path.
	 * @param Path $Path
	 * @return bool
	 */
	public final function isChildOf(Path $Path): bool {
		return preg_match('/^' . preg_quote($Path, '/') . '/', $this->toString()) > 0;
	}

	/**
	 * Determines if the current path is a parent of another one path.
	 * @param Path $Path
	 * @return bool
	 */
	public final function isParentOf(Path $Path){
		return $Path->isChildOf($this);
	}

	/**
	 * Determines if path is equal to another one path.
	 * @param Path $Path
	 * @return bool
	 */
	public final function isEqualTo(Path $Path){
		return strlen(trim($this->exclude($Path), DIRECTORY_SEPARATOR)) < 1;
	}

	/**
	 * @const int
	 */
	public const TIF_FILE = 0b0001;

	/**
	 * @const int
	 */
	public const TIF_NOT_FILE = 0b0010;

	/**
	 * @const int
	 */
	public const TIF_DIRECTORY = 0b0100;

	/**
	 * @const int
	 */
	public const TIF_NOT_DIRECTORY = 0b1000;

	/**
	 * @const int
	 */
	public const TIF_LINK = 0b00010000;

	/**
	 * @const int
	 */
	public const TIF_NOT_LINK = 0b00100000;

	/**
	 * @const int
	 */
	public const TIF_EXIST = 0b01000000;

	/**
	 * @const int
	 */
	public const TIF_NOT_EXIST = 0b10000000;

	/**
	 * @const int
	 */
	public const TIF_READABLE = 0b000100000000;

	/**
	 * @const int
	 */
	public const TIF_NOT_READABLE = 0b001000000000;

	/**
	 * @const int
	 */
	public const TIF_WRITABLE = 0b010000000000;

	/**
	 * @const int
	 */
	public const TIF_NOT_WRITABLE = 0b100000000000;

	/**
	 * @const int
	 */
	public const TIF_ABSOLUTE = 0b0001000000000000;

	/**
	 * @const int
	 */
	public const TIF_NOT_ABSOLUTE = 0b0010000000000000;

	/**
	 * @const int
	 */
	public const TIF_DOT = 0b0100000000000000;

	/**
	 * @param callable $Handler
	 * @param int $mode
	 * @return Path
	 * @throws Exception
	 */
	public final function try(callable $Handler, int $mode = 0): Path {
		if ($mode & self::TIF_FILE && $this->isFile()
			|| $mode & self::TIF_DIRECTORY && $this->isDirectory()
			|| $mode & self::TIF_NOT_FILE && !$this->isFile()
			|| $mode & self::TIF_NOT_DIRECTORY && !$this->isDirectory()
			|| $mode & self::TIF_LINK && $this->isLink()
			|| $mode & self::TIF_NOT_LINK && !$this->isLink()
			|| $mode & self::TIF_EXIST && $this->isExists()
			|| $mode & self::TIF_NOT_EXIST && !$this->isExists()
			|| $mode & self::TIF_READABLE && $this->isReadable()
			|| $mode & self::TIF_NOT_READABLE && !$this->isReadable()
			|| $mode & self::TIF_WRITABLE && $this->isWritable()
			|| $mode & self::TIF_NOT_WRITABLE && !$this->isWritable()
			|| $mode & self::TIF_ABSOLUTE && $this->isAbsolute()
			|| $mode & self::TIF_NOT_ABSOLUTE && !$this->isAbsolute()
			|| $mode & self::TIF_DOT && $this->isDot()) {
				call_user_func($Handler, $this);
		}

		return $this;
	}

}
