<?php

declare(strict_types=1);

namespace Semperton\Multibase;

final class Base62 extends Transcoder
{
	public function __construct()
	{
		parent::__construct('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
	}
}
