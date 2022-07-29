<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Semperton\Multibase\Base85;

final class Base85Test extends TestCase
{
	public function testHelloWorld(): void
	{
		$transcoder = new Base85();
		$data = 'Hello World';

		$encoded = $transcoder->encode($data);

		$this->assertEquals('7KPt{PR^XAyM<W', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testUnicode(): void
	{
		$transcoder = new Base85();
		$data = 'Hello, 世界';

		$encoded = $transcoder->encode($data);

		$this->assertEquals('%uJ|<6#K5c<7qI0h', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testZeroByte(): void
	{
		$transcoder = new Base85();
		$data = chr(0);

		$encoded = $transcoder->encode($data);

		$this->assertEquals('0', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testLeadingZeros(): void
	{
		$transcoder = new Base85();
		$data = chr(0) . chr(0) . chr(0) . chr(0) . chr(255) . chr(255) . chr(255) . chr(255);

		$encoded = $transcoder->encode($data);

		$this->assertEquals('0000|NsC0', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testMultipleBytes(): void
	{
		$transcoder = new Base85();
		$data = chr(255) . chr(255) . chr(255) . chr(255) . chr(0) . chr(0) . chr(0) . chr(0);

		$encoded = $transcoder->encode($data);

		$this->assertEquals('_sw2<`kSC0', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, $decoded);
	}

	public function testIpv6(): void
	{
		$transcoder = new Base85();
		$data = '1080::8:800:200c:417a';

		$encoded = $transcoder->encode(inet_pton($data));

		$this->assertEquals('4)+k&C#VzJ4br>0wv%Yp', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, inet_ntop($decoded));

		$data = '2001:db8:100:f101::1';

		$encoded = $transcoder->encode(inet_pton($data));

		$this->assertEquals('9R}vSQZ1W=8fRv3*HAqn', $encoded);

		$decoded = $transcoder->decode($encoded);
		$this->assertEquals($data, inet_ntop($decoded));
	}
}
