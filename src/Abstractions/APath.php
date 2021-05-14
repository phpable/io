<?php
namespace Able\IO\Abstractions;

use \Able\Helpers\Src;
use \Able\Helpers\Env;

use \Able\Prototypes\ICallable;
use \Able\Prototypes\TCallable;

use \Able\IO\Abstractions\IBehavior;
use \Able\IO\Abstractions\IPatchable;

use \Able\IO\Behavior\BUnix;
use \Able\IO\Behavior\BWindows;

use \Exception;

abstract class APath
	implements ICallable, IBehavior, IPatchable {

	use TCallable;

	/**
	 * @var string|null
	 */
	private static ?string $BehaviorClass = null;

	/**
	 * @return string
	 * @throws Exception
	 */
	protected final static function detectBehaviorClass(): string {
		if (!is_null(self::$BehaviorClass)){
			return self::$BehaviorClass;
		}

		if (!class_exists(self::$BehaviorClass = Src::lns(__NAMESPACE__)
			. '\Behavior\B' . ucfirst(Env::name()))){

				throw new Exception('Unsupported environment!');
		}

		return self::$BehaviorClass;
	}

	/**
	 * @var IBehavior
	 */
	private IBehavior $Behavior;

	/**
	 * @return IBehavior
	 * @throws Exception
	 */
	protected final function getBehavior(): IBehavior {
		if (!isset($this->Behavior)) {
			$this->Behavior = Src::make(self::detectBehaviorClass());
		}

		return $this->Behavior;
	}

	/**
	 * @param string $name
	 * @param array $Args
	 * @return mixed
	 * @throws Exception
	 */
	public final function call(string $name, array $Args = []): mixed {
		if (!method_exists($this->getBehavior(), $name)){
			throw new Exception('Undefined method ' .  $name);
		}

		return $this->getBehavior()->{$name}($this, ...$Args);
	}

	/**
	 * @param string $fragment
	 * @return null|string
	 * @throws Exception
	 */
	public final static function detectPoint(string $fragment): ?string {
		return self::detectBehaviorClass()::detectPoint($fragment);
	}

	/**
	 * @param string $fragment
	 * @return string
	 * @throws Exception
	 */
	public final static function removePoint(string $fragment): string {
		return self::detectBehaviorClass()::removePoint($fragment);
	}

}

