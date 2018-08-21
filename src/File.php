<?php
namespace Able\IO;

use \Able\IO\Abstractions\ANode;
use \Able\IO\Abstractions\ISource;

use \Able\IO\Reader;
use \Able\IO\Writer;
use \Able\IO\ReadingBuffer;
use \Able\IO\WritingBuffer;

final class File extends ANode
	implements ISource {

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

			file_put_contents($Path->toString(), '');
		}

		parent::__construct($Path);
	}

	/**
	 * @return File
	 */
	public final function purge(): File {
		file_put_contents($this->toString(), '');
		return $this;
	}

	/**
	 * @return string
	 */
	public final function getContent(): string {
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
	public final function toWriter(): Writer {
		return new Writer($this);
	}

	/**
	 * @return ReadingBuffer
	 */
	public final function toReadingBuffer(): ReadingBuffer {
		return new ReadingBuffer($this);
	}

	/**
	 * @return WritingBuffer
	 * @throws \Exception
	 */
	public final function toWritingBuffer(): WritingBuffer {
		return WritingBuffer::create($this->toReader()->read());
	}
}
