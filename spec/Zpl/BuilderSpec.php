<?php

namespace spec\Zebra\Zpl;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Zebra\Contracts\Zpl\Image as ImageContract;

class BuilderSpec extends ObjectBehavior
{
    function it_creates_zpl_commands_with_string_number_and_boolean_parameters()
    {
        $this->command('BC', 'N', 100, true)->toZpl()->shouldReturn("^XA^BCN,100,Y^XZ");
    }

    function it_creates_zpl_commands_from_magic_methods_and_their_arguments()
    {
        $this->fo(50, 50)->toZpl()->shouldReturn("^XA^FO50,50^XZ");
    }

    function it_accepts_a_single_argument_of_image_for_gf_command(ImageContract $image)
    {
        $image->toAscii()->willReturn('FF00');
        $image->width()->willReturn(2);
        $image->height()->willReturn(16);

        $this->gf($image)->toZpl()->shouldReturn("^XA^GFA,32,32,2,FF00^XZ");
    }

    function it_can_be_converted_to_a_string()
    {
        $this->fo(50, 50)->__toString()->shouldReturn("^XA^FO50,50^XZ");
    }
}
