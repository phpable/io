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
	 * @const int
	 */
	public const WM_SKIP_EMPTY = 0b0001;

	/**
	 * @const int
	 */
	public const WM_SKIP_INDENT = 0b0010;

	/**
	 * @param Generator $Input
	 * @param int $mode
	 * @throws \Exception
	 */
	public final function write(\Generator $Input, int $mode = 0): void {
		if (!is_resource($handler = fopen($this->File->toString(), 'a'))){
			throw new \Exception('Invalid source!');
		}

		try{
			foreach ($Input as $line) {
				/**
				 * If the WP_SKIP_INDENT flag is set any leading
				 * or ending whitespace characters will be removed.
				 */
				if ($mode & self::WM_SKIP_INDENT){
					$line = trim($line);
				}

				/**
				 * if the WP_SKIP_EMPTY flag is set, any empty strings
				 * will be ignored.
				 */
				if (!empty(trim($line)) || !($mode & self::WM_SKIP_EMPTY)) {
					fputs($handler, rtrim($line) . "\n");
				}
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
