<?php

declare(strict_types=1);

namespace Semperton\Multibase\Exception;

use RuntimeException;

class InvalidCharsException extends RuntimeException
{
	/** @var string[] */
	protected ?array $chars = null;

	/**
	 * @param string[] $chars
	 */
	public function setChars(array $chars): void
	{
		$this->chars = $chars;
	}

	/**
	 * @return null|string[]
	 */
	public function getChars(): ?array
	{
		return $this->chars;
	}
}
