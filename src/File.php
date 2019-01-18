<?php
namespace Able\IO;

use \Able\IO\Abstractions\ANode;
use \Able\IO\Abstractions\ISource;
use \Able\IO\Abstractions\ILocated;
use \Able\IO\Abstractions\IPatchable;

use \Able\IO\Reader;
use \Able\IO\Writer;
use \Able\IO\ReadingBuffer;
use \Able\IO\ReadingStream;
use \Able\IO\WritingBuffer;

use \Exception;

final class File extends ANode
	implements ISource, ILocated {

	/**
	 * @param Path $Path
	 * @throws Exception
	 */
	public final function __construct(Path $Path) {
		if ($Path->isDirectory() || $Path->isLink()) {
			throw new Exception(sprintf('Path is not a regular file: %s!', $Path->toString()));
		}

		if (!$Path->isExists()) {
			if (!$Path->getParent()->isWritable()){
				throw new Exception(sprintf('Path is not exists or not writable: %s!', $Path->toString()));
			}

			file_put_contents($Path->toString(), '');
		}

		parent::__construct($Path);
	}

	/**
	 * @return File
	 * @throws Exception
	 */
	public final function purge(): File {
		file_purge($this->assemble(), LOCK_EX);
		return $this;
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public final function remove(): void {
		if (!@unlink($this->assemble())){
			throw new Exception('Cannot remove the file: ' .  $this->toString() . '!');
		}
	}

	/**
	 * @param string $name
	 * @return void
	 * @throws Exception
	 */
	public final function rename(string $name): void {
		rename($this->assemble(), $this->toPath()->changeEnding($name)
			->try(function(Path $Path) {
				throw new Exception(sprintf('File already exists: %s!', $Path));
		}, Path::TIF_EXIST)->toString());
	}

	/**
	 * @param string $content
	 * @throws Exception
	 */
	public final function append(string $content): void {
		file_put_contents($this->assemble(),
			$content, LOCK_EX | FILE_APPEND);
	}

	/**
	 * @param string $content
	 * @throws Exception
	 */
	public final function rewrite(string $content): void {
		$this->purge();

		file_put_contents($this->assemble(),
			$content, LOCK_EX);
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public final function getContent(): string {
		return file_get_contents($this->assemble());
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public final function getLocation(): string {
		return $this->assemble();
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public final function getExtension(): string {
		return preg_replace('/^.*\./', '', basename($this->getBaseName()));
	}

	/**
	 * @return int
	 * @throws Exception
	 */
	public function getModifiedTime() {
		return (int)filemtime($this->assemble());
	}

	/**
	 * @return int
	 * @throws Exception
	 */
	public final function getSize(): int {
		return (int)filesize($this->assemble());
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public final function getMimeType(): string {
		return mime_content_type($this->assemble());
	}

	/**
	 * @return Reader
	 * @throws Exception
	 */
	public final function toReader(): Reader {
		return new Reader($this);
	}

	/**
	 * @return Writer
	 * @throws Exception
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
	 * @return ReadingStream
	 */
	public final function toReadingStream(): ReadingStream {
		return new ReadingStream($this);
	}

	/**
	 * @return WritingBuffer
	 * @throws Exception
	 */
	public final function toWritingBuffer(): WritingBuffer {
		return WritingBuffer::create($this->toReader()->read());
	}

	/**
	 * @param IPatchable $Destination
	 * @param bool $rewrite
	 * @return void
	 *
	 * @throws Exception
	 */
	public final function copy(IPatchable $Destination, bool $rewrite = false): void {
		copy($this->assemble(), $Destination->toPath()
			->try(function (Path $Path) {
				$Path->append($this->getBaseName());
		}, Path::TIF_DIRECTORY)
			->try(function() use ($rewrite) {

				if (!$rewrite) {
					throw new Exception('Destination already exists!');
				}
		}, Path::TIF_EXIST));
	}

	/**
	 * @param IPatchable $Destination
	 * @throws Exception
	 */
	public final function move(IPatchable $Destination): void {
		$this->copy($Destination, true);
		$this->remove();
	}
}
