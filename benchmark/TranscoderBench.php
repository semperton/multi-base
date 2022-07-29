<?php

declare(strict_types=1);

use Semperton\Multibase\Base58;
use Semperton\Multibase\Base62;
use Semperton\Multibase\Base85;

/**
 * @Iterations(5)
 * @Warmup(2)
 * @OutputTimeUnit("seconds")
 * @OutputMode("throughput")
 */
final class MatcherBench
{
	protected string $data;

	protected Base58 $base58;

	protected Base62 $base62;

	protected Base85 $base85;

	public function __construct()
	{
		$this->data = random_bytes(128);
		$this->base58 = new Base58();
		$this->base62 = new Base62();
		$this->base85 = new Base85();
	}

	/**
	 * @Revs(100)
	 */
	public function benchBase58(): void
	{
		$this->base58->encode($this->data);
	}

	/**
	 * @Revs(100)
	 */
	public function benchBase62(): void
	{
		$this->base62->encode($this->data);
	}

	/**
	 * @Revs(100)
	 */
	public function benchBase85(): void
	{
		$this->base85->encode($this->data);
	}
}
