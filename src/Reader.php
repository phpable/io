<?php
namespace Able\IO;

use \Generator;

use \Able\IO\File;
use \Able\IO\Abstractions\IReader;

use \Able\Prototypes\TStringable;

class Reader implements IReader {
	use TStringable;

	/**
	 * @var File
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

	/**
	 * @return string
	 */
	public final function toString(): string {
		return $this->File->toString();
	}
}
