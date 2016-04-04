<?php

namespace Zebra\Zpl;

use RuntimeException;
use Zebra\Contracts\Zpl\Image as ImageContract;

class Image implements ImageContract
{
    /**
     * The GD image resource.
     *
     * @var resource
     */
    protected $image;

    /**
     * The image width in pixels.
     *
     * @var int
     */
    protected $width;

    /**
     * The image height in pixels.
     *
     * @var int
     */
    protected $height;

    /**
     * The ASCII hexadecimal encoded image data.
     *
     * @var string
     */
    protected $encoded;

    /**
     * Create an instance.
     *
     * @param string $data
     */
    public function __construct($data)
    {
        $this->image = $this->create($data);
        $this->width = imagesx($this->image);
        $this->height = imagesy($this->image);
    }

    /**
     * Destroy an instance.
     */
    public function __destruct()
    {
        imagedestroy($this->image);
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function width()
    {
        return (int)ceil($this->width / 8);
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function height()
    {
        return $this->height;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function toAscii()
    {
        return $this->encoded ?: $this->encoded = $this->encode();
    }

    /**
     * Create a new GD image from the supplied string.
     *
     * @param $data
     * @return resource
     */
    protected function create($data)
    {
        if (false === $image = imagecreatefromstring($data)) {
            throw new RuntimeException('Could not read image.');
        }

        if (!imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        imagefilter($image, IMG_FILTER_GRAYSCALE);

        return $image;
    }

    /**
     * Encode the image in ASCII hexadecimal by looping over every pixel.
     *
     * @return string
     */
    protected function encode()
    {
        $bitmap = null;
        $lastRow = null;

        for ($y = 0; $y < $this->height; $y++) {
            $bits = null;

            for ($x = 0; $x < $this->width; $x++) {
                $bits .= (imagecolorat($this->image, $x, $y) & 0xFF) < 127 ? '1' : '0';
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
    protected function compress($row, $lastRow)
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
    protected function compressTrailingZerosOrOnes($row)
    {
        return preg_replace(['/0+$/', '/F+$/'], [',', '!'], $row);
    }

    /**
     * Compress characters which repeat.
     *
     * @param string $row
     * @return string
     */
    protected function compressRepeatingCharacters($row)
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
