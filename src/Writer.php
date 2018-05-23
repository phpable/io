<?php
namespace Able\IO;

use \Generator;

use \Able\IO\File;
use \Able\IO\Abstractions\IWriter;

use \Able\Prototypes\TStringable;

class Writer implements IWriter {
	use TStringable;

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
	 * @param Generator $Input
	 * @throws \Exception
	 */
	public final function write(\Generator $Input): void {
		if (!is_resource($handler = fopen($this->File->toString(), 'a'))){
			throw new \Exception('Invalid source!');
		}

		try{
			foreach ($Input as $line) {
				fputs($handler, rtrim($line) . "\n");
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
