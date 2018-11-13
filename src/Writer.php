<?php
namespace Able\IO;

use \Able\IO\File;

use \Able\IO\Abstractions\IWriter;
use \Able\IO\Abstractions\AAccessor;

use \Able\Helpers\Str;

use \Exception;
use \Generator;

class Writer extends AAccessor
	implements IWriter {

	/**
	 * @const int
	 */
	public const WM_REPLACE = 0b0001;

	/**
	 * @const int
	 */
	public const WM_SKIP_EMPTY = 0b0010;

	/**
	 * @const int
	 */
	public const WM_SKIP_INDENT = 0b0100;

	/**
	 * @const int
	 */
	public const WM_SKIP_ENDING = 0b1000;

	/**
	 * @param Generator $Input
	 * @param int $mode
	 *
	 * @throws Exception
	 */
	public final function write(Generator $Input, int $mode = 0): void {
		if (!is_resource($handler = fopen($this->File->toString(), 'r+'))){
			throw new Exception('Invalid source!');
		}

		try{
			foreach ($Input as $line) {

				/**
				 * If the WP_SKIP_INDENT flag is set any leading
				 * or ending whitespace characters will be removed.
				 */
				if ($mode & self::WM_SKIP_INDENT){
					$line = ltrim($line);
				}


				/**
				 * If the WP_SKIP_INDENT flag is set any leading
				 * or ending whitespace characters will be removed.
				 */
				if ($mode & self::WM_SKIP_ENDING){
					$line = rtrim($line);
				}

				/**
				 * if the WP_SKIP_EMPTY flag is set, any empty strings
				 * will be ignored.
				 */
				if (!empty(trim($line)) || ~$mode & self::WM_SKIP_EMPTY) {
					if (~$mode & self::WM_REPLACE){
						fseek($handler, 0, SEEK_END);
					}

					fputs($handler, Str::break($line));
				}
			}
		}finally{
			fclose($handler);
		}
	}
}
