<?php

declare(strict_types=1);

namespace Semperton\Multibase;

use Semperton\Multibase\Transcoder\BaseTranscoder;

/**
 * RFC 1924
 * @see https://datatracker.ietf.org/doc/html/rfc1924
 */
final class Base85 extends BaseTranscoder
{
	public function __construct()
	{
		parent::__construct('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!#$%&()*+-;<=>?@^_`{|}~');
	}
}
