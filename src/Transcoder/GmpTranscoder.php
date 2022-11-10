<?php

declare(strict_types=1);

namespace Semperton\Multibase\Transcoder;

use Semperton\Multibase\Exception\DublicateCharsException;
use Semperton\Multibase\Exception\InvalidAlphabetException;
use Semperton\Multibase\Exception\InvalidCharsException;

use function mb_str_split;
use function array_diff;
use function array_diff_key;
use function array_unique;
use function array_flip;
use function array_combine;
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

	/** @var array<string, string> */
	protected array $dict;

	/** @var array<string, string> */
	protected array $flipped;

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
			$exception->setChars(array_unique($chars));
			throw $exception;
		}

		$this->dict = $this->getDictionary($alpha);
		$this->flipped = array_flip($this->dict);
	}

	/**
	 * @param string[] $alphabet
	 * @return array<string, string>
	 */
	protected function getDictionary(array $alphabet): array
	{
		$gmp = substr(self::GMP, 0, $this->base);

		if ($this->base <= 36) {
			$gmp = strtolower($gmp);
		}

		return array_combine(str_split($gmp), $alphabet);
	}

	public function encode(string $string): string
	{
		if ($string === '') {
			return $string;
		}

		$hex = bin2hex($string);

		$data = gmp_strval(gmp_init($hex, 16), $this->base);
		$result = strtr($data, $this->dict);

		// zero padding
		for ($i = 0; isset($hex[$i + 2]) && $hex[$i] === '0' && $hex[$i + 1] === '0'; $i += 2) {
			/** @psalm-suppress InvalidArrayOffset */
			$result = $this->dict['0'] . $result;
		}

		return $result;
	}

	public function decode(string $string): string
	{
		if ($string === '') {
			return $string;
		}

		if ($chars = array_diff(mb_str_split($string), $this->dict)) {
			$exception = new InvalidCharsException('String contains invalid chars');
			/** @var string[] $chars */
			$exception->setChars($chars);
			throw $exception;
		}

		$replaced = strtr($string, $this->flipped);
		$hex = gmp_strval(gmp_init($replaced, $this->base), 16);

		if (strlen($hex) % 2) {
			$hex = '0' . $hex;
		}

		// zeros
		for ($i = 0; isset($replaced[$i + 1]) && $replaced[$i] === '0'; $i++) {
			$hex =  '00' . $hex;
		}

		return (string)hex2bin($hex);
	}
}
