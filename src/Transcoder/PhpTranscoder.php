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
use function array_reverse;
use function array_values;
use function array_flip;
use function count;
use function unpack;
use function pack;

final class PhpTranscoder implements TranscoderInterface
{
	protected int $base;

	/** @var array<int, string> */
	protected array $alphabet;

	/** @var array<string, int> */
	protected array $flipped;

	public function __construct(string $alphabet)
	{
		/** @var array<int, string> */
		$this->alphabet = mb_str_split($alphabet);
		$this->base = count($this->alphabet);

		if ($this->base < 2) {
			throw new InvalidAlphabetException('Alphabet must contain at least two chars');
		}

		if ($chars = array_diff_key($this->alphabet, array_unique($this->alphabet))) {
			$exception = new DublicateCharsException('Alphabet contains dublicate chars');
			$exception->setChars(array_values(array_unique($chars)));
			throw $exception;
		}

		$this->flipped = array_flip($this->alphabet);
	}

	/**
	 * @param int[] $values
	 * @return int[]
	 */
	public static function convert(array $values, int $fromBase, int $toBase): array
	{
		$count = count($values);
		$result = [];

		while ($count > 0) {

			$remainder = 0;
			$length = 0;

			for ($i = 0; $i < $count; ++$i) {

				$remainder = $remainder * $fromBase + $values[$i];

				if ($remainder >= $toBase) {

					$values[$length++] = (int)($remainder / $toBase);
					$remainder = $remainder % $toBase;
				} else if ($length > 0) {
					$values[$length++] = 0;
				}
			}

			$count = $length;
			$result[] = $remainder;
		}

		return array_reverse($result);
	}

	public function encode(string $string): string
	{
		$data = unpack('C*', $string);

		/** @var int[] */
		$data = array_values($data);

		$converted = $this->convert($data, 256, $this->base);

		$result = '';
		foreach ($converted as $index) {
			$result .= $this->alphabet[$index];
		}

		// zero padding
		for ($i = 0; isset($data[$i + 1]) && $data[$i] === 0; $i++) {
			$result = $this->alphabet[0] . $result;
		}

		return $result;
	}

	public function decode(string $string): string
	{
		/** @var string[] */
		$data = mb_str_split($string);

		if ($chars = array_diff($data, $this->alphabet)) {
			$exception = new InvalidCharsException('String contains invalid chars');
			$exception->setChars(array_values(array_unique($chars)));
			throw $exception;
		}

		foreach ($data as $index => $char) {
			$data[$index] = $this->flipped[$char];
		}

		/** @var int[] $data */
		$converted = $this->convert($data, $this->base, 256);

		// zero padding
		$zeros = [];
		for ($i = 0; isset($data[$i + 1]) && $data[$i] === 0; $i++) {
			$zeros[] = 0;
		}

		return pack('C*', ...$zeros, ...$converted);
	}
}
