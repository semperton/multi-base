<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Semperton\Multibase\Base62;

final class Base62Test extends TestCase
{
	public function testHelloWorld(): void
	{
		$transcoder = new Base62();
		$data = 'Hello World';

		$encoded = $transcoder->encode($data);

		$this->assertEquals('73XpUgyMwkGr29M', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testUnicode(): void
	{
		$transcoder = new Base62();
		$data = 'Hello, 世界';

		$encoded = $transcoder->encode($data);

		$this->assertEquals('1wJfrzvdbuFbL65vcS', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testZeroByte(): void
	{
		$transcoder = new Base62();
		$data = chr(0);

		$encoded = $transcoder->encode($data);

		$this->assertEquals('0', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testLeadingZeros(): void
	{
		$transcoder = new Base62();
		$data = chr(0) . chr(0) . chr(0) . chr(0) . chr(255) . chr(255) . chr(255) . chr(255);

		$encoded = $transcoder->encode($data);

		$this->assertEquals('00004gfFC3', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testMultipleBytes(): void
	{
		$transcoder = new Base62();
		$data = chr(255) . chr(255) . chr(255) . chr(255) . chr(0) . chr(0) . chr(0) . chr(0);

		$encoded = $transcoder->encode($data);

		$this->assertEquals('LygHZwPV2MC', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testMd5(): void
	{
		$transcoder = new Base62();
		$data = 'Hello World';
		$md5 = md5($data);

		$encoded = $transcoder->encode(md5($data, true));

		$this->assertEquals('5O4SoozqXEOwlYtvkC5zkr', $encoded);

		$decoded = $transcoder->decode($encoded);

		$this->assertEquals($md5, bin2hex($decoded));
	}
}
