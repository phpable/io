<?php
namespace Able\IO;

use \Able\IO\Abstractions\ANode;

final class File extends ANode {

	/**
	 * @param Path $Path
	 * @throws \Exception
	 */
	public final function __construct(Path $Path) {
		if ($Path->isDir()) {
			throw new \Exception('Path "' . $Path->toString() . '" is a directory!');
		}

		if ($Path->isLink()) {
			throw new \Exception('Path "' . $Path->toString() . '" is a link!');
		}

		if (!$Path->isExists()) {
			if (!$Path->getParent()->toDerectory()->getPath()->isWritable()){
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
}
