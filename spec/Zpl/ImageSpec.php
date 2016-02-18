<?php namespace spec\Zebra\Zpl;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;

class ImageSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(file_get_contents(__DIR__ . '/../test_150.png'));
    }

    function it_converts_images_to_compressed_ascii_hexadecimal_bitmaps()
    {
        $this->toAscii()->shouldReturn(file_get_contents(__DIR__ . '/../test_150.txt'));
    }

    function it_converts_large_images_to_compressed_ascii_hexadecimal_bitmaps()
    {
        $this->beConstructedWith(file_get_contents(__DIR__ . '/../test_1000.png'));

        $this->toAscii()->shouldReturn(file_get_contents(__DIR__ . '/../test_1000.txt'));
    }

    function it_gets_the_height_of_the_image_in_rows()
    {
        $this->height()->shouldReturn(75);
    }

    function it_gets_the_width_of_the_image_in_bytes()
    {
        $this->width()->shouldReturn(19);
    }
}
