<?php
namespace Able\IO\Abstractions;

interface ISource {

	/**
	 * @return string
	 */
	public function getContent(): string;

}
