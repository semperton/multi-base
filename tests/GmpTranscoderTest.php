<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Semperton\Multibase\Exception\DublicateCharsException;
use Semperton\Multibase\Exception\InvalidCharsException;
use Semperton\Multibase\Transcoder\GmpTranscoder;

final class GmpTranscoderTest extends TestCase
{
	protected function setUp(): void
	{
		if (!function_exists('gmp_init')) {
			$this->markTestSkipped('GMP extension is not installed');
		}
	}

	public function testHex(): void
	{
		$transcoder = new GmpTranscoder('0123456789abcdef');
		$data = 'Hello World';

		$encoded = $transcoder->encode($data);

		$this->assertEquals(bin2hex($data), $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);

		$data = chr(0) . chr(0) . chr(0) . chr(0) . chr(255) . chr(255) . chr(255) . chr(255);

		$encoded = $transcoder->encode($data);

		// need to pad zeros for hex
		$encoded = str_pad($encoded, strlen($data) * 2, '0', STR_PAD_LEFT);

		$this->assertEquals(bin2hex($data), $encoded);
	}

	public function testMultibyte(): void
	{
		$transcoder = new GmpTranscoder(
			'🧳🌂☂️🧵🪡🪢🧶👓🕶🥽🥼🦺👔👕👖🧣🧤🧥🧦👗👘🥻🩴🩱🩲' .
				'🩳👙👚👛👜👝🎒👞👟🥾🥿👠👡🩰👢👑👒🎩🎓🧢⛑🪖💄💍💼'
		);
		$data = 'Hello World';

		$encoded = $transcoder->encode($data);

		$this->assertEquals('☂🪢👟🩴🩰🥻👚👙🧢🩲🧥🥽🎩👙👝🎒', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testInvalidDecodeChars(): void
	{
		try {
			$transcoder = new GmpTranscoder('0123456789abcdef');
			$transcoder->decode('🥽1Acf==♪');
		} catch (InvalidCharsException $ex) {
			$this->assertSame(['🥽', 'A', '=', '♪'], $ex->getChars());
		}
	}

	public function testDublicateAlphabetChars(): void
	{
		try {
			$transcoder = new GmpTranscoder('aBCadeffa');
		} catch (DublicateCharsException $ex) {
			$this->assertSame(['a', 'f'], $ex->getChars());
		}
	}

	public function testEmptyDecodeString(): void
	{
		$transcoder = new GmpTranscoder('0123456789');
		$encoded = $transcoder->decode('');

		$this->assertEquals('', $encoded);
	}

	public function testEmptyEncodeString(): void
	{
		$transcoder = new GmpTranscoder('0123456789');
		$encoded = $transcoder->encode('');

		$this->assertEquals('', $encoded);
	}
}
