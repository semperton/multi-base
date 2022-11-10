<div align="center">
<a href="https://github.com/semperton">
<img width="140" src="https://raw.githubusercontent.com/semperton/.github/main/readme-logo.svg" alt="Semperton">
</a>
<h1>Semperton Multibase</h1>
<p>Base transcoder with multibyte character alphabet support.</p>
</div>

---

## Installation

Just use Composer:

```
composer require semperton/multibase
```
Multibase requires PHP 7.4+ and the mbstring extension

> Tip: Install the GMP extension for faster base conversion up to base 62

## Package
Included base transcoders:
- Base58
- Base62
- Base85 RFC 1924

## Usage
Multibase transcoders are able to convert any arbitrary data.
Use them as URL / MD5 shorteners, password generators, Base64 alternatives...

```php
use Semperton\Multibase\Base62;
use Semperton\Multibase\Base85;

// basic usage
$base62 = new Base62();
$base62->encode('Hello World'); // 73XpUgyMwkGr29M
$base62->decode('73XpUgyMwkGr29M'); // Hello World

// shorten md5 hash
$hash = md5('Hello World'); // b10a8db164e0754105b7a99be72e3fe5

$short = $base62->encode(hex2bin($hash)); // 5O4SoozqXEOwlYtvkC5zkr
$short = $base62->encode(md5('Hello World', true)); // same as above

$decoded = $base62->decode($short);
$hash === bin2hex($decoded); // true

// password generation
$bytes = openssl_random_pseudo_bytes(16);
$password = (new Base85())->encode($bytes); // e.g. Ncg>RWSYO+2t@~G8PO0J

```

## Custom transcoders
You can create custom transcoders with your own alphabets (multibyte support).
Just for fun, how about an emoji transcoder?

```php
use Semperton\Multibase\Transcoder\BaseTranscoder;

$emojiTranscoder = new BaseTranscoder(
	'🧳🌂☂️🧵🪡🪢🧶👓🕶🥽🥼🦺👔👕👖🧣🧤🧥🧦👗👘🥻🩴🩱🩲' .
	'🩳👙👚👛👜👝🎒👞👟🥾🥿👠👡🩰👢👑👒🎩🎓🧢⛑🪖💄💍💼'
);

$encoded = $emojiTranscoder->encode('Hello World'); // ☂🪢👟🩴🩰🥻👚👙🧢🩲🧥🥽🎩👙👝🎒
$emojiTranscoder->decode($encoded); // Hello World
```
