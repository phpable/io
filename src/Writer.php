<?php
namespace Able\IO;

use \Able\IO\File;

use \Able\IO\Abstractions\IWriter;
use \Able\IO\Abstractions\IReader;
use \Able\IO\Abstractions\AAccessor;

use \Exception;
use \Generator;

class Writer extends AAccessor
	implements IWriter {

	/**
	 * @const int
	 */
	public const WM_SKIP_BREAKS = 0b0001;

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
		if (!is_resource($handler = fopen($this->File->toString(), 'a'))) {
			throw new Exception('Invalid source!');
		}

		try{
			foreach ($Input as $line) {

				/**
				 * If the WP_SKIP_INDENT flag is set on,
				 * leading whitespace characters are removed.
				 */
				if ($mode & self::WM_SKIP_INDENT) {
					$line = ltrim($line);
				}

				/**
				 * If the WM_SKIP_ENDING flag is set on,
				 * ending whitespace characters are removed.
				 */
				if ($mode & self::WM_SKIP_ENDING) {
					$line = rtrim($line);
				}

				/**
				 * If the WP_SKIP_EMPTY flag is set on,
				 * empty strings are ignored.
				 */
				if (!empty(trim($line))
					|| ~$mode & self::WM_SKIP_EMPTY) {

						fwrite($handler, $line);

						/**
						 * If the WM_SKIP_BREAKS flag is set on,
						 * no EOL characters will be added.
						 */
						if (~$mode & self::WM_SKIP_BREAKS) {
							fwrite($handler, PHP_EOL);
						}
				}
			}
		} finally {
			fclose($handler);
		}
	}

	/**
	 * @param IReader $Reader
	 * @throws Exception
	 */
	public final function consume(IReader $Reader): void {
		if (!is_resource($handler = fopen($this->File->toString(), 'a'))) {
			throw new Exception('Invalid source!');
		}

		try {

			/**
			 * Each line from a reader consuming AS IS
			 * without changes and transformations.
			 */
			foreach ($Reader->read() as $line) {
				fputs($handler, rtrim($line, "\n\t") . PHP_EOL);
			}

		} catch (\Throwable $Exception) {
			fclose($handler);
		}
	}
}
