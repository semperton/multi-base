<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Semperton\Multibase\Transcoder\BaseTranscoder;

final class BaseTranscoderTest extends TestCase
{
	public function testIntTranscode(): void
	{
		$transcoder = new BaseTranscoder('abcdef');
		$num = PHP_INT_MAX;

		$encoded = $transcoder->intEncode($num);
		$decoded = $transcoder->intDecode($encoded);

		$this->assertEquals($num, $decoded);
	}
}
