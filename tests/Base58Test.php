<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Semperton\Multibase\Base58;

final class Base58Test extends TestCase
{
	public function testHelloWorld(): void
	{
		$transcoder = new Base58();
		$data = 'Hello World';

		$encoded = $transcoder->encode($data);

		$this->assertEquals('JxF12TrwUP45BMd', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testUnicode(): void
	{
		$transcoder = new Base58();
		$data = 'Hello, 世界';

		$encoded = $transcoder->encode($data);

		$this->assertEquals('72k1xXWG5AsuJ7FFns', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testZeroByte(): void
	{
		$transcoder = new Base58();
		$data = chr(0);

		$encoded = $transcoder->encode($data);

		$this->assertEquals('1', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testLeadingZeros(): void
	{
		$transcoder = new Base58();
		$data = chr(0) . chr(0) . chr(0) . chr(0) . chr(255) . chr(255) . chr(255) . chr(255);
		
		$encoded = $transcoder->encode($data);

		$this->assertEquals('11117YXq9G', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testMultipleBytes(): void
	{
		$transcoder = new Base58();
		$data = chr(255) . chr(255) . chr(255) . chr(255) . chr(0) . chr(0) . chr(0) . chr(0);

		$encoded = $transcoder->encode($data);

		$this->assertEquals('jpXCZY5jqM9', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}
}
