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
        $this->__toString()->shouldReturn('004,:02E8,03F8,:13F9,1BFB,3IF8,JFE,7IFC,3IF8,1IF,0FFE,07FC,0E4E,004,::');
    }

    function it_ignores_the_colour_palette_order_of_the_incoming_image()
    {
        $this->beConstructedWith(file_get_contents(__DIR__ . '/../test.gif'));

        $this->__toString()->shouldReturn('FFBFE,:FD17E,FC07E,:EC06E,E404E,CI06,,8I02,CI06,EI0E,F001E,F803E,F1B1E,FFBFE,::');
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
