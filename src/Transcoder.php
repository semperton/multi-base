<?php

declare(strict_types=1);

namespace Semperton\Multibase;

use Semperton\Multibase\Exception\DublicateCharsException;
use Semperton\Multibase\Exception\InvalidAlphabetException;
use Semperton\Multibase\Exception\InvalidCharsException;

class Transcoder implements TranscoderInterface
{
	protected int $base;

	/** @var string[] */
	protected array $alphabet;

	/** @var null|int[] */
	protected ?array $flipped = null;

	public function __construct(string $alphabet)
	{
		/** @var string[] */
		$this->alphabet = mb_str_split($alphabet);
		$this->base = count($this->alphabet);

		if ($this->base < 2) {
			throw new InvalidAlphabetException('Alphabet must contain at least two chars');
		}

		if ($chars = array_diff_key($this->alphabet, array_unique($this->alphabet))) {
			$exception = new DublicateCharsException('Alphabet contains dublicate chars');
			$exception->setChars(array_unique($chars));
			throw $exception;
		}
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

			$divide = 0;
			$newlen = 0;

			for ($i = 0; $i < $count; ++$i) {

				$divide = $divide * $fromBase + $values[$i];

				if ($divide >= $toBase) {

					$values[$newlen++] = (int)($divide / $toBase);
					$divide = $divide % $toBase;
				} else if ($newlen > 0) {
					$values[$newlen++] = 0;
				}
			}

			$count = $newlen;
			$result[] = $divide;
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

		// unlike str_split, mb_str_split returns empty array on empty string
		// so we are save here...
		if ($chars = array_diff($data, $this->alphabet)) {
			$exception = new InvalidCharsException('String contains invalid chars');
			$exception->setChars($chars);
			throw $exception;
		}

		if (!$this->flipped) {
			/** @var int[] */
			$this->flipped = array_flip($this->alphabet);
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
