<?php
namespace Able\IO;

use \Able\IO\File;

use \Able\IO\Abstractions\IReader;
use \Able\IO\Abstractions\AAccessor;

use \Able\Helpers\Str;

class Reader extends AAccessor
	implements IReader {

	/**
	 * @var int
	 */
	private $index = self::DEFAULT_INDEX;

	/**
	 * @return int
	 */
	public final function getIndex(): int {
		return $this->index;
	}

	/**
	 * @return \Generator
	 * @throws \Exception
	 */
	public function read(): \Generator {
		if (!is_resource($handler = fopen($this->File->toString(), 'r'))){
			throw new \Exception('Invalid source!');
		}

		$this->index = 0;
		try{
			while(($line = fgets($handler)) !== false){
				yield ++$this->index => Str::unbreak($line);
			}
		}finally{
			fclose($handler);
		}
	}

	/**
	 * @return \Generator
	 * @throws \Exception
	 */
	public final function iterate(): \Generator {
		return $this->read();
	}
}
