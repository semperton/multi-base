<?php

declare(strict_types=1);

namespace Semperton\Multibase\Transcoder;

use Semperton\Multibase\Exception\DublicateCharsException;
use Semperton\Multibase\Exception\InvalidAlphabetException;
use Semperton\Multibase\Exception\InvalidCharsException;

use function mb_str_split;
use function array_diff_key;
use function array_unique;
use function array_flip;
use function array_combine;
use function array_values;
use function preg_match_all;
use function count;
use function substr;
use function strtolower;
use function str_split;
use function bin2hex;
use function hex2bin;
use function gmp_init;
use function gmp_strval;
use function strtr;
use function strlen;

final class GmpTranscoder implements TranscoderInterface
{
	const GMP = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

	protected int $base;

	protected string $regex;

	/** @var array<string, string> */
	protected ?array $dict = null;

	/** @var array<string, string> */
	protected ?array $flipped = null;

	public function __construct(string $alphabet)
	{
		/** @var string[] */
		$alpha = mb_str_split($alphabet);
		$this->base = count($alpha);

		if ($this->base < 2 || $this->base > 62) {
			throw new InvalidAlphabetException('Alphabet length must be between 2 and 62');
		}

		if ($chars = array_diff_key($alpha, array_unique($alpha))) {
			$exception = new DublicateCharsException('Alphabet contains dublicate chars');
			$exception->setChars(array_values(array_unique($chars)));
			throw $exception;
		}

		$this->regex = '/[^' . preg_quote($alphabet) . ']/u';

		$gmp = substr(self::GMP, 0, $this->base);

		if ($this->base <= 36) {
			$gmp = strtolower($gmp);
		}

		if ($gmp !== $alphabet) {
			/** @var array<string, string> */
			$this->dict = array_combine(str_split($gmp), $alpha);
			$this->flipped = array_flip($this->dict);
		}
	}

	public function encode(string $string): string
	{
		if ($string === '') {
			return $string;
		}

		$hex = bin2hex($string);

		$result = gmp_strval(gmp_init($hex, 16), $this->base);
		$zero = '0';

		if ($this->dict) {
			$result = strtr($result, $this->dict);
			/** @psalm-suppress InvalidArrayOffset */
			$zero = $this->dict['0'];
		}

		// zero padding
		for ($i = 0; isset($hex[$i + 2]) && $hex[$i] . $hex[$i + 1] === '00'; $i += 2) {
			$result = $zero . $result;
		}

		return $result;
	}

	public function decode(string $string): string
	{
		if ($string === '') {
			return $string;
		}

		if (preg_match_all($this->regex, $string, $matches)) {
			$exception = new InvalidCharsException('String contains invalid chars');
			$exception->setChars(array_values(array_unique($matches[0])));
			throw $exception;
		}

		if ($this->flipped) {
			$string = strtr($string, $this->flipped);
		}

		$hex = gmp_strval(gmp_init($string, $this->base), 16);

		if (strlen($hex) % 2) {
			$hex = '0' . $hex;
		}

		// zeros
		for ($i = 0; isset($string[$i + 1]) && $string[$i] === '0'; $i++) {
			$hex =  '00' . $hex;
		}

		return (string)hex2bin($hex);
	}
}
