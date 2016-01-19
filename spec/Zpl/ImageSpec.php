<?php namespace spec\Zebra\Zpl;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;

class ImageSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(file_get_contents(__DIR__ . '/../test_150.png'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Zebra\Zpl\Image');
    }

    function it_converts_images_to_compressed_ascii_hexadecimal_bitmaps()
    {
        $this->__toString()->shouldReturn(file_get_contents(__DIR__ . '/../test_150.txt'));
    }

    function it_converts_large_images_to_compressed_ascii_hexadecimal_bitmaps()
    {
        $this->beConstructedWith(file_get_contents(__DIR__ . '/../test_1000.png'));

        $this->__toString()->shouldReturn(file_get_contents(__DIR__ . '/../test_1000.txt'));
    }

    function it_gets_the_dimensions_of_the_image_in_pixels()
    {
        $this->width()->shouldReturn(150);

        $this->height()->shouldReturn(75);
    }

    function it_gets_the_width_of_the_image_in_bytes()
    {
        $this->widthInBytes()->shouldReturn(19);
    }
}
