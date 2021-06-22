<?php
namespace Able\IO\Behavior;

use \Able\IO\Abstractions\IBehavior;
use \Able\IO\Path;

use \Exception;

class BUnix implements IBehavior {

	/**
	 * @param string $fragment
	 * @return string|null
	 */
	public final static function detectPoint(string $fragment): ?string {
		return preg_match('/^\\/+/', $fragment) ? '/' : null;
	}

	/**
	 * @param string $fragment
	 * @return string
	 */
	public final static function removePoint(string $fragment): string{
		return preg_replace('/^\\/+/', '', $fragment);
	}

	/**
	 * @param Path $Path
	 * @return Path
	 * @throws Exception
	 */
	public final function makeAbsolute(Path $Path): Path {
		return $Path->prepend('/');
	}

}
