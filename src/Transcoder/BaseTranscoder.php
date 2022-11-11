<?php

declare(strict_types=1);

namespace Semperton\Multibase\Transcoder;

use function mb_strlen;
use function function_exists;
use function pack;
use function unpack;

class BaseTranscoder implements TranscoderInterface
{
	protected TranscoderInterface $transcoder;

	public function __construct(string $alphabet)
	{
		$base = mb_strlen($alphabet);

		if ($base <= 62 && function_exists('gmp_init')) {
			$this->transcoder = new GmpTranscoder($alphabet);
		} else {
			$this->transcoder = new PhpTranscoder($alphabet);
		}
	}

	public function encode(string $string): string
	{
		return $this->transcoder->encode($string);
	}

	public function decode(string $string): string
	{
		return $this->transcoder->decode($string);
	}

	public function intEncode(int $num): string
	{
		return $this->transcoder->encode(pack('J', $num));
	}

	public function intDecode(string $string): int
	{
		$data = $this->transcoder->decode($string);
		return (int)unpack('J', $data)[1];
	}
}
