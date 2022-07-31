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
final class TranscoderBench
{
	protected string $data;

	protected Base58 $base58;

	protected Base62 $base62;

	protected Base85 $base85;

	protected string $base58str;

	protected string $base62str;

	protected string $base85str;

	public function __construct()
	{
		$this->data = random_bytes(128);
		$this->base58 = new Base58();
		$this->base62 = new Base62();
		$this->base85 = new Base85();

		$this->base58str = $this->base58->encode($this->data);
		$this->base62str = $this->base62->encode($this->data);
		$this->base85str = $this->base85->encode($this->data);
	}

	/**
	 * @Revs(100)
	 * @Groups({"encoders"})
	 */
	public function benchBase58encode(): void
	{
		$this->base58->encode($this->data);
	}

	/**
	 * @Revs(100)
	 * @Groups({"encoders"})
	 */
	public function benchBase62encode(): void
	{
		$this->base62->encode($this->data);
	}

	/**
	 * @Revs(100)
	 * @Groups({"encoders"})
	 */
	public function benchBase85encode(): void
	{
		$this->base85->encode($this->data);
	}

	/**
	 * @Revs(100)
	 * @Groups({"decoders"})
	 */
	public function benchBase58decode(): void
	{
		$this->base58->decode($this->base58str);
	}

	/**
	 * @Revs(100)
	 * @Groups({"decoders"})
	 */
	public function benchBase62decode(): void
	{
		$this->base62->decode($this->base62str);
	}

	/**
	 * @Revs(100)
	 * @Groups({"decoders"})
	 */
	public function benchBase85decode(): void
	{
		$this->base85->decode($this->base85str);
	}
}
