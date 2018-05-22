<?php
namespace Able\Struct\Tests;

use \Able\IO\Path;
use \Eggbe\Helper\Env;
use \PHPUnit\Framework\TestCase;

class PathTest extends TestCase {

	/**
	 * @throws \Exception
	 */
	public final function testCreateFromString() {
		$Path = new Path(__FILE__);

		$this->assertEquals($Path->toString(), __FILE__);
		$this->assertEquals($Path->count(), preg_match_all('/' . preg_quote(DIRECTORY_SEPARATOR, '/') . '+/', __FILE__));

		$index = 0;
		foreach (preg_split('/' . preg_quote(DIRECTORY_SEPARATOR, '/') . '+/',
			Path::removePoint(__FILE__), -1, PREG_SPLIT_NO_EMPTY) as $fragment){
				$this->assertEquals($Path->toArray()[$index++], $fragment);
		}
	}

	/**
	 * @throws \Exception
	 */
	public final function testCreateFromArray(){
		$Path = new Path($e = array_merge((array)Path::detectPoint(__FILE__), preg_split('/'
			. preg_quote(DIRECTORY_SEPARATOR, '/') . '+/', __FILE__, -1, PREG_SPLIT_NO_EMPTY)));

		$this->assertEquals($Path->toString(), __FILE__);
	}

	/**
	 * @throws \Exception
	 */
	public final function testHierarchy(){
		$file = __FILE__;

		$Path = new Path($file);
		$point = Path::detectPoint($file);

		while(($file = dirname($file)) != $point){
			$this->assertEquals($file, (string)($Path = $Path->getParent()));
		}
	}

	/**
	 * @throws \Exception
	 */
	public final function testPlatformFeatures(){
		$Path = new Path();

		foreach (preg_split('/' . preg_quote(DIRECTORY_SEPARATOR, '/')
			. '+/', Path::removePoint(__FILE__), -1, PREG_SPLIT_NO_EMPTY) as $fragment){
				$Path->append($fragment);
		}

		if (Env::name() == Env::EP_UNIX){
			$this->assertEquals((string)$Path->makeAbsolute(), __FILE__);
		}

		if (Env::name() == Env::EP_WINDOWS){
			$this->assertEquals((string)$Path->changePoint(Path::detectPoint(__FILE__)), __FILE__);
		}
	}
}
