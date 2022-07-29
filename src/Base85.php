<?php

declare(strict_types=1);

namespace Semperton\Multibase;

final class Base85 extends Transcoder
{
	/**
	 * @see https://datatracker.ietf.org/doc/html/rfc1924
	 */
	public function __construct()
	{
		parent::__construct('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!#$%&()*+-;<=>?@^_`{|}~');
	}
}
