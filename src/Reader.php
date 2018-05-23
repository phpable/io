<?php
namespace Able\IO;

use \Generator;

use \Able\IO\File;
use \Able\IO\Abstractions\IReader;

class Reader implements IReader {

	/**
	 * @var null
	 */
	private $File = null;

	/**
	 * Reader constructor.
	 * @param \Able\IO\File $File
	 */
	public final function __construct(File $File) {
		$this->File = $File;
	}

	/**
	 * @return Generator
	 * @throws \Exception
	 */
	public final function read(): Generator {
		if (!is_resource($handler = fopen($this->File->toString(), 'r'))){
			throw new \Exception('Invalid source!');
		}

		try{
			while(($line = fgets($handler)) !== false){
				yield $line;
			}
		}finally{
			fclose($handler);
		}
	}
}
