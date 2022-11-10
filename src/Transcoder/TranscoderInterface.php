<?php

declare(strict_types=1);

namespace Semperton\Multibase\Transcoder;

interface TranscoderInterface
{
	public function encode(string $string): string;
	public function decode(string $string): string;
}
