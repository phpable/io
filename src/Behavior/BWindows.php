<?php
namespace Able\IO\Behavior;

use \Able\IO\Abstractions\IBehavior;
use \Able\IO\Path;

class BWindows implements IBehavior {

	/**
	 * @param string $fragment
	 * @return string|null
	 */
	public final static function detectPoint(string $fragment): ?string {
		return preg_match('/^([A-Z]:\\\?)/', $fragment, $Matches)
			? $Matches[1] : null;
	}

	/**
	 * @param string $fragment
	 * @return string
	 */
	public final static function removePoint(string $fragment): string{
		return preg_replace('/^[A-Z]:\\\*/', null, $fragment);
	}

	/**
	 * @param Path $Path
	 * @param string $point
	 * @return Path
	 * @throws \Exception
	 */
	public final function changePoint(Path $Path, string $point): Path {
		if (!preg_match('/^[A-Z]:?\\\?$/', $point)){
			throw new \Exception('Invalid reference!');
		}

		return $Path->prepend(rtrim($point, ':\\') . ':');
	}
}
