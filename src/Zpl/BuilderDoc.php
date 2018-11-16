<?php

namespace Zebra\Zpl;

/**
 * Class BuilderDoc
 *
 * @method self mm(string $a, bool $b = false) Print Mode
 * @method self ft(int $x, int $y, int $z = 0) Field Typeset
 * @method self fd(string $a) Field Data
 * @method self fb(int $a, int $b = 1, int $c = 0, string $d = 'L', int $e = 0) Field Block
 * @method self fh(string $a = '_') Field Hexadecimal Indicator
 * @method self fo(int $x, int $y, int $z = 0) Field Origin
 * @method self fs() Field Separator
 * @method self pw(int $a) Print Width
 * @method self ll(int $y) Label Length
 * @method self ls(int $a) Label Shift
 * @method self ci(int $a, $s1 = 0, $d1 = 0, $s2 = 0, $d2 = 0) Change International Font/Encoding
 * @method self a0(string $o, int $h, int $w, string $d = 'R', $f = '', $x = '') Use Font Name to Call Font
 * @method self by(int $w, float $r = 3.0, int $h = 10) Bar Code Field Default
 * @method self b3(string $o, bool $e = false, int $h = 10, bool $f = true, bool $g = false) Code 39 Bar Code
 * @method self gb(int $w, int $h, int $t = 1, string $c = 'B', int $r = 0) Graphic Box
 * @method self wv(bool $e = false) Verify RFID Encoding Operation
 * @method self rf(string $o, string $f = 'H', int $b = 0, int $n = 1, string $m = 'E') Label Shift
 * @method self pq(int $q, int $p = 0, int $r = 0, bool $o = false, bool $e = true) Print Quantity
 * @method self gf(Image $image) Print Quantity
 */
class BuilderDoc extends Builder
{

}