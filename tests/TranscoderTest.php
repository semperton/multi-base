<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Semperton\Multibase\Transcoder;

final class TranscoderTest extends TestCase
{
	public function testHex(): void
	{
		$transcoder = new Transcoder('0123456789abcdef');
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
		$transcoder = new Transcoder(
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
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('String contains invalid chars < A= >');

		$transcoder = new Transcoder('0123456789abcdef');
		$transcoder->decode('1Acf=');
	}

	public function testDublicateAlphabetChars(): void
	{
		$this->expectException(InvalidArgumentException::class);
		$this->expectExceptionMessage('Alphabet has dublicate chars < af >');

		$transcoder = new Transcoder('aBCadeff');
	}
}
