<?php
namespace Able\IO;

use \Able\IO\File;

use \Able\IO\Abstractions\IReader;
use \Able\IO\Abstractions\AAccessor;

class Reader extends AAccessor
	implements IReader {

	/**
	 * @return \Generator
	 * @throws \Exception
	 */
	public final function read(): \Generator {
		if (!is_resource($handler = fopen($this->File->toString(), 'r'))){
			throw new \Exception('Invalid source!');
		}

		try{
			while(($line = fgets($handler)) !== false){
				yield rtrim($line);
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
