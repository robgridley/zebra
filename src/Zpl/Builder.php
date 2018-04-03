<?php

namespace Zebra\Zpl;

use Zebra\Contracts\Zpl\Image as ImageContract;

class Builder
{
    /**
     * ZPL commands.
     *
     * @var array
     */
    protected $zpl = [];

    /**
     * Add a command.
     *
     * @return Builder
     */
    public function command(): self
    {
        $parameters = func_get_args();
        $command = strtoupper(array_shift($parameters));
        $parameters = array_map([$this, 'parameter'], $parameters);

        $this->zpl[] = '^' . $command . implode(',', $parameters);

        return $this;
    }

    /**
     * Convert native types to their ZPL representations.
     *
     * @param mixed $parameter
     * @return mixed
     */
    protected function parameter($parameter)
    {
        if (is_bool($parameter)) {
            return $parameter ? 'Y' : 'N';
        }

        return $parameter;
    }

    /**
     * Handle dynamic method calls.
     *
     * @param string $method
     * @param array $arguments
     * @return Builder
     */
    public function __call($method, $arguments)
    {
        array_unshift($arguments, $method);

        return call_user_func_array([$this, 'command'], $arguments);
    }

    /**
     * Add GF command.
     *
     * @return Builder
     */
    public function gf(): self
    {
        $arguments = func_get_args();

        if (func_num_args() === 1 && ($image = $arguments[0]) instanceof ImageContract) {
            $bytesPerRow = $image->width();
            $byteCount = $fieldCount = $bytesPerRow * $image->height();

            return $this->command('GF', 'A', $byteCount, $fieldCount, $bytesPerRow, $image->toAscii());
        }

        array_unshift($arguments, 'GF');

        return call_user_func_array([$this, 'command'], $arguments);
    }

    /**
     * Convert instance to ZPL.
     *
     * @param bool $newlines
     * @return string
     */
    public function toZpl(bool $newlines = false): string
    {
        return implode($newlines ? "\n" : '', array_merge(['^XA'], $this->zpl, ['^XZ']));
    }

    /**
     * Convert instance to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toZpl();
    }
}
