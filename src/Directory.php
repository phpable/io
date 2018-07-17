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
	public final function list(): \Generator{
		$h = opendir($this->toString());

		try{
			while(($file = readdir($h)) !== false){
				yield new Path($file);
			}
		} finally {
			closedir($h);
		}
	}
}
