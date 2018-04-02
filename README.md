# Zebra

PHP ZPL builder, image conversion and a basic client for network-connected Zebra label printers.

Requires: PHP 7.1.0+

* Convert images to ASCII hex.
* Create ZPL code in PHP that is easy to read.
* Simple wrapper for PHP sockets to send ZPL to the printer via raw TCP/IP (port 9100).

## Example

The following example will print a label with an image positioned 50 dots from the top left.

```php
use Zebra\Client;
use Zebra\Zpl\Image;
use Zebra\Zpl\Builder;
use Zebra\Zpl\GdDecoder;

$decoder = GdDecoder::fromPath('example.png');
$image = new Image($decoder);

$zpl = new Builder();
$zpl->fo(50, 50)->gf($image)->fs();

$client = new Client('10.0.0.50');
$client->send($zpl);
```

## Installation with Composer

```
$ composer require robgridley/zebra
```
