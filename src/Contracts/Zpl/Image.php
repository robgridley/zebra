<?php

namespace Zebra\Contracts\Zpl;

interface Image
{
    /**
     * Get the image width in bytes.
     *
     * @return int
     */
    public function width(): int;

    /**
     * Get the image height in pixels.
     *
     * @return int
     */
    public function height(): int;

    /**
     * Get the ASCII hex representation of the image.
     *
     * @return string
     */
    public function toAscii(): string;
}
