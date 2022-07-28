<?php

declare(strict_types=1);

namespace Semperton\Multibase;

final class Base58 extends Transcoder
{
	public function __construct()
	{
		parent::__construct('123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
	}
}
