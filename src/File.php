<?php
namespace Able\IO;

use \Able\IO\Abstractions\ANode;
use \Able\IO\Abstractions\ISource;
use \Able\IO\Abstractions\ILocated;

use \Able\IO\Reader;
use \Able\IO\Writer;
use \Able\IO\ReadingBuffer;
use \Able\IO\WritingBuffer;

final class File extends ANode
	implements ISource, ILocated {

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
	 * @throws \Exception
	 */
	public final function purge(): File {
		file_put_contents($this->assemble(), '');
		return $this;
	}

	/**
	 * @return void
	 * @throws \Exception
	 */
	public final function remove(): void {
		if (!@unlink($this->assemble())){
			throw new \Exception('Cannot remove the file: ' .  $this->toString() . '!');
		}
	}

	/**
	 * @param string $content
	 * @throws \Exception
	 */
	public final function append(string $content): void {
		file_put_contents($this->assemble(),
			$content, LOCK_EX | FILE_APPEND);
	}

	/**
	 * @param string $content
	 * @throws \Exception
	 */
	public final function rewrite(string $content): void {
		file_put_contents($this->assemble(),
			$content, LOCK_EX);
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public final function getContent(): string {
		return file_get_contents($this->assemble());
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	public final function getLocation(): string {
		return $this->assemble();
	}

	/**
	 * @return string
	 */
	public final function getExtension(): string {
		return preg_replace('/^.*\./', '', basename($this->getBaseName()));
	}

	/**
	 * @return int
	 * @throws \Exception
	 */
	public function getModifiedTime() {
		return (int)filemtime($this->assemble());
	}

	/**
	 * @return int
	 * @throws \Exception
	 */
	public final function getFileSize(){
		return (int)filesize($this->assemble());
	}

	/**
	 * @return Reader
	 * @throws \Exception
	 */
	public final function toReader(): Reader {
		return new Reader($this);
	}

	/**
	 * @return Writer
	 * @throws \Exception
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
