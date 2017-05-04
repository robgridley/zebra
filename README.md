# Zebra

PHP ZPL builder, image conversion and a basic client for network-connected Zebra label printers.

Requires: PHP 5.5.9+ with the GD module.

* Create ZPL code in PHP that is easy to read.
* Convert images to ASCII hex.
* Simple wrapper for PHP sockets to send ZPL to the printer via raw TCP/IP (port 9100).

## Example

The following example will print a label with an image positioned 50 dots from the top left.

```php
use Zebra\Client;
use Zebra\Zpl\Image;
use Zebra\Zpl\Builder;

$image = new Image(file_get_contents('example.png'));

$zpl = new Builder();
$zpl->fo(50, 50)->gf($image)->fs();

$client = new Client('10.0.0.50');
$client->send($zpl);
```

## Installation with Composer

```
$ composer require robgridley/zebra
```
