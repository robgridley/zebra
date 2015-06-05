<?php namespace Zebra\Zpl;

class Image
{
    /**
     * The image identifier.
     *
     * @var resource
     */
    protected $image;

    /**
     * Create an instance.
     *
     * @param $image
     */
    public function __construct($image)
    {
        $this->image = imagecreatefromstring($image);

        $this->dither();
    }

    /**
     * Convert the image to 2 colours with dithering.
     */
    protected function dither()
    {
        if ( ! imageistruecolor($this->image)) {
            imagepalettetotruecolor($this->image);
        }

        imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        imagetruecolortopalette($this->image, true, 2);
    }

    /**
     * The width in bytes.
     *
     * @return float
     */
    public function widthInBytes()
    {
        return (int) ceil($this->width() / 8);
    }

    /**
     * The width in pixels.
     *
     * @return int
     */
    public function width()
    {
        return imagesx($this->image);
    }

    /**
     * The height in pixels.
     *
     * @return int
     */
    public function height()
    {
        return imagesy($this->image);
    }

    /**
     * Get the bitmap for the image by looping over every pixel.
     *
     * @return string
     */
    protected function getBitmap()
    {
        $rows = $this->height();
        $columns = $this->width();
        $bitmap = null;

        for ($row = 0; $row < $rows; $row++) {

            $bits = null;

            for ($column = 0; $column < $columns; $column++) {
                $bits .= imagecolorat($this->image, $column, $row) ? '0' : '1';
            }

            $bitmap .= $this->pack($bits);
        }

        return $bitmap;
    }

    /**
     * Convert a binary string to ASCII hexadecimal numbers, two digits per byte.
     *
     * @param $bits
     * @return string
     */
    protected function pack($bits)
    {
        $bytes = str_split($bits, 8);

        // Pad the last byte with zeros.
        $bytes[] = str_pad(array_pop($bytes), 8, '0');

        $callback = function ($byte) {
            return sprintf('%02X', bindec($byte));
        };

        return implode(null, array_map($callback, $bytes));
    }

    /**
     * Get the instance as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getBitmap();
    }

}
