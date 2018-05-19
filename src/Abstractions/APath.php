<?php
namespace Able\IO\Abstractions;

use \Eggbe\Helper\Src;
use \Eggbe\Helper\Env;

use \Able\Prototypes\ICallable;
use \Able\Prototypes\TCallable;

use \Able\IO\Abstractions\IBehavior;

use \Able\IO\Behavior\BUnix;
use \Able\IO\Behavior\BWindows;

abstract class APath implements ICallable, IBehavior {
	use TCallable;

	/**
	 * @var string
	 */
	private static $BehaviorClass = null;

	/**
	 * @return string
	 * @throws \Exception
	 */
	protected final static function detectBehaviorClass(): string {
		if (!is_null(self::$BehaviorClass)){
			return self::$BehaviorClass;
		}

		if (!class_exists(self::$BehaviorClass = Src::lns(__NAMESPACE__)
			. '\Behavior\B' . ucfirst(Env::name()))){
				throw new \Exception('Unsupported environment!');
		}

		return self::$BehaviorClass;
	}

	/**
	 * @var IBehavior
	 */
	private $Behavior = null;

	/**
	 * @return IBehavior
	 * @throws \Exception
	 */
	protected final function getBehavior(): IBehavior {
		if (is_null($this->Behavior)) {
			$this->Behavior = Src::make(self::detectBehaviorClass());
		}

		return $this->Behavior;
	}

	/**
	 * @param string $name
	 * @param array $Args
	 * @return mixed
	 * @throws \Exception
	 */
	public final function call(string $name, array $Args = []) {
		if (is_null(self::$BehaviorClass)){
			return forward_static_call([self::class, '__callStatic'], $name, $Args);
		}

		if (!method_exists($this->getBehavior(), $name)){
			throw new \Exception('Undefined method ' .  $name);
		}

		return $this->getBehavior()->{$name}($this, ...$Args);
	}

	/**
	 * @param string $fragment
	 * @return null|string
	 * @throws \Exception
	 */
	public final static function detectPoint(string $fragment): ?string {
		return self::detectBehaviorClass()::detectPoint($fragment);
	}

	/**
	 * @param string $fragment
	 * @return string
	 * @throws \Exception
	 */
	public final static function removePoint(string $fragment): string {
		return self::detectBehaviorClass()::removePoint($fragment);
	}

}

