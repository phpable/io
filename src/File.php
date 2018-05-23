<?php
namespace Able\IO;

use \Able\IO\Abstractions\ANode;

use \Able\IO\Reader;
use \Able\IO\Writer;

final class File extends ANode {

	/**
	 * @param Path $Path
	 * @throws \Exception
	 */
	public final function __construct(Path $Path) {
		if ($Path->isDirectory()) {
			throw new \Exception('Path "' . $Path->toString() . '" is a directory!');
		}

		if ($Path->isLink()) {
			throw new \Exception('Path "' . $Path->toString() . '" is a link!');
		}

		if (!$Path->isExists()) {
			if (!$Path->getParent()->isWritable()){
				throw new \Exception('Path "' . $Path->toString() . '" is not exists or not writable!');
			}

			file_put_contents($Path->toString(), null);
		}

		parent::__construct($Path);
	}

	/**
	 * @return string
	 */
	public function getContent() : string {
		return file_get_contents($this->toString());
	}

	/**
	 * @return string
	 */
	public final function getExtension(): string {
		return preg_replace('/^.*\./', '', basename($this->getBaseName()));
	}

	/**
	 * @return Reader
	 */
	public final function toReader() : Reader {
		return new Reader($this);
	}

	/**
	 * @return Writer
	 */
	public final function toWriter():Writer{
		return new Writer($this);
	}
}
