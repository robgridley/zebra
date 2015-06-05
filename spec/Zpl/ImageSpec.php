<?php namespace spec\Zebra\Zpl;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;

class ImageSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(file_get_contents(__DIR__ . '/../test.png'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Zebra\Zpl\Image');
    }

    function it_converts_images_to_ascii_hexadecimal_bitmaps()
    {
        $this->__toString()->shouldReturn('00400000400002E80003F80003F80013F9001BFB003FFF80FFFFE07FFFC03FFF801FFF000FFE0007FC000E4E00004000004000004000');
    }

    function it_ignores_the_colour_palette_order_of_the_incoming_image()
    {
        $this->beConstructedWith(file_get_contents(__DIR__ . '/../test.gif'));

        $this->__toString()->shouldReturn('FFBFE0FFBFE0FD17E0FC07E0FC07E0EC06E0E404E0C00060000000800020C00060E000E0F001E0F803E0F1B1E0FFBFE0FFBFE0FFBFE0');
    }

    function it_gets_the_dimensions_of_the_image_in_pixels()
    {
        $this->width()->shouldReturn(19);

        $this->height()->shouldReturn(18);
    }

    function it_gets_the_width_of_the_image_in_bytes()
    {
        $this->widthInBytes()->shouldReturn(3);
    }
}
