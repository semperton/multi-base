<?php

declare(strict_types=1);

namespace Semperton\Multibase\Exception;

use InvalidArgumentException;

class InvalidCharsException extends InvalidArgumentException
{
	/** @var string[] */
	protected array $chars = [];

	/**
	 * @param string[] $chars
	 */
	public function setChars(array $chars): void
	{
		$this->chars = $chars;
	}

	/**
	 * @return string[]
	 */
	public function getChars(): array
	{
		return $this->chars;
	}
}
