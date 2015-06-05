# Zebra
PHP ZPL builder and a basic client for network-connected Zebra label printers.

Requires: PHP 5.6 with the GD module.

* Create ZPL code in PHP that is clean and easy to read.
* Convert images to ASCII hex bitmaps (JPEG, PNG, GIF, WBMP, and GD2 supported).
* Simple wrapper for PHP sockets to send ZPL to the printer via raw TCP/IP (port 9100).

## Examples
The following example will print a label with an image positioned 50 dots from the top left.
```php
use Zebra\Client;
use Zebra\Zpl\Image;
use Zebra\Zpl\Builder;

$zpl = new Builder();
$zpl->fo(50, 50);

$image = new Image(file_get_contents('example.png'));
$zpl->gf($image);

$client = new Client('10.0.0.50');
$client->send($zpl);
```
The same example using static constructors and method chaining:
```php
$image = new Image(file_get_contents('example.png'));
$zpl = Zpl::start()->fo(50, 50)->gf($image);
Client::printer('10.0.0.50')->send($zpl);
```
