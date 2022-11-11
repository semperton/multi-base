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
		$this->data = hex2bin('180fd50878b59f33863de469d0feaaa3e42a202d9e6ecec870c63edeef9b0b72221dabae110ac68d7bbfc016cd4b9913ea149e1c1bd66cea7cb47edbc2948a386efdb44242059e595421b2002f74a4de0a8f6d70434bf199741d468d5c21aef4bb83e107ca5d6c9f49d094a0a52f096abe9f3c55cfbce724abc9fd2072e76eab');
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
	public function benchBase58intEncode(): void
	{
		$this->base58->intEncode(PHP_INT_MAX);
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
	 * @Groups({"decoders"})
	 */
	public function benchBase58decode(): void
	{
		$this->base58->decode($this->base58str);
	}

	/**
	 * @Revs(100)
	 * @Groups({"encoders"})
	 */
	public function benchBase62intEncode(): void
	{
		$this->base62->intEncode(PHP_INT_MAX);
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
	 * @Groups({"decoders"})
	 */
	public function benchBase62decode(): void
	{
		$this->base62->decode($this->base62str);
	}

	/**
	 * @Revs(100)
	 * @Groups({"encoders"})
	 */
	public function benchBase85intEncode(): void
	{
		$this->base85->intEncode(PHP_INT_MAX);
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
	public function benchBase85decode(): void
	{
		$this->base85->decode($this->base85str);
	}
}
