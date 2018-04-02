<?php

namespace Zebra\Zpl;

use Zebra\Contracts\Zpl\Decoder;
use Zebra\Contracts\Zpl\Image as ImageContract;

class Image implements ImageContract
{
    /**
     * The decoder instance.
     *
     * @var resource
     */
    protected $decoder;

    /**
     * The ASCII hexadecimal encoded image data.
     *
     * @var string
     */
    protected $encoded;

    /**
     * The image width (in pixels).
     *
     * @var int
     */
    protected $width;

    /**
     * The image height (in pixels).
     *
     * @var int
     */
    protected $height;

    /**
     * Create a new image instance.
     *
     * @param Decoder $decoder
     */
    public function __construct(Decoder $decoder)
    {
        $this->width = $decoder->width();
        $this->height = $decoder->height();
        $this->decoder = $decoder;
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function width(): int
    {
        return (int)ceil($this->width / 8);
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function height(): int
    {
        return $this->height;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function toAscii(): string
    {
        return $this->encoded ?: $this->encoded = $this->encode();
    }

    /**
     * Encode the image in ASCII hexadecimal by looping over every pixel.
     *
     * @return string
     */
    protected function encode(): string
    {
        $bitmap = null;
        $lastRow = null;

        for ($y = 0; $y < $this->height; $y++) {
            $bits = null;

            for ($x = 0; $x < $this->width; $x++) {
                $bits .= $this->decoder->getBitAt($x, $y);
            }

            $bytes = str_split($bits, 8);
            $bytes[] = str_pad(array_pop($bytes), 8, '0');

            $row = null;

            foreach ($bytes as $byte) {
                $row .= sprintf('%02X', bindec($byte));
            }

            $bitmap .= $this->compress($row, $lastRow);
            $lastRow = $row;
        }

        return $bitmap;
    }

    /**
     * Compress a row of ASCII hexadecimal data.
     *
     * @param string $row
     * @param string $lastRow
     * @return string
     */
    protected function compress(string $row, ?string $lastRow): string
    {
        if ($row === $lastRow) {
            return ':';
        }

        $row = $this->compressTrailingZerosOrOnes($row);
        $row = $this->compressRepeatingCharacters($row);

        return $row;
    }

    /**
     * Replace trailing zeros or ones with a comma (,) or exclamation (!) respectively.
     *
     * @param string $row
     * @return string
     */
    protected function compressTrailingZerosOrOnes(string $row): string
    {
        return preg_replace(['/0+$/', '/F+$/'], [',', '!'], $row);
    }

    /**
     * Compress characters which repeat.
     *
     * @param string $row
     * @return string
     */
    protected function compressRepeatingCharacters(string $row): string
    {
        $callback = function ($matches) {
            $original = $matches[0];
            $repeat = strlen($original);
            $count = null;

            if ($repeat > 400) {
                $count .= str_repeat('z', floor($repeat / 400));
                $repeat %= 400;
            }

            if ($repeat > 19) {
                $count .= chr(ord('f') + floor($repeat / 20));
                $repeat %= 20;
            }

            if ($repeat > 0) {
                $count .= chr(ord('F') + $repeat);
            }

            return $count . substr($original, 1, 1);
        };

        return preg_replace_callback('/(.)(\1{2,})/', $callback, $row);
    }
}
