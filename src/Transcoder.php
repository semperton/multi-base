<?php

declare(strict_types=1);

namespace Semperton\Multibase;

use InvalidArgumentException;

class Transcoder implements TranscoderInterface
{
	/** @var string[] */
	protected array $alphabet;
	protected int $base;

	public function __construct(string $alphabet)
	{
		/** @var string[] */
		$this->alphabet = mb_str_split($alphabet);
		$this->base = count($this->alphabet);

		if ($this->base < 2) {
			throw new InvalidArgumentException('Alphabet must contain at least two chars');
		}

		if ($diff = array_diff_key($this->alphabet, array_unique($this->alphabet))) {
			throw new InvalidArgumentException('Alphabet has dublicate chars < ' . implode('', array_unique($diff)) . ' >');
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

		if ($diff = array_diff($data, $this->alphabet)) {
			throw new InvalidArgumentException('String contains invalid chars < ' . implode('', $diff) . ' >');
		}

		/** @var int[] */
		$flipped = array_flip($this->alphabet);

		foreach ($data as $index => $char) {
			$data[$index] = $flipped[$char];
		}

		/** @var int[] $data */
		$converted = $this->convert($data, $this->base, 256);

		// zero padding
		for ($i = 0; isset($data[$i + 1]) && $data[$i] === 0; $i++) {
			array_unshift($converted, 0);
		}

		return pack('C*', ...$converted);
	}
}
