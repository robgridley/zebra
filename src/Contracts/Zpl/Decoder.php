<?php

namespace Zebra\Contracts\Zpl;

interface Decoder
{
    /**
     * Get the width of the image (in pixels).
     *
     * @return int
     */
    public function width(): int;

    /**
     * Get the height of the image (in pixels).
     *
     * @return int
     */
    public function height(): int;

    /**
     * Get the bit at the specified position.
     *
     * @param int $x
     * @param int $y
     * @return int
     */
    public function getBitAt(int $x, int $y): int;
}
