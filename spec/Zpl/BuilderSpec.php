<?php namespace spec\Zebra\Zpl;

use Zebra\Zpl\Image;
use Prophecy\Argument;
use PhpSpec\ObjectBehavior;

class BuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Zebra\Zpl\Builder');
    }

    function it_creates_zpl_commands_with_string_number_and_boolean_parameters()
    {
        $this->command('BC', 'N', 100, true)->__toString()->shouldReturn("^XA\n^BCN,100,Y\n^XZ");
    }

    function it_creates_zpl_commands_from_magic_methods_and_their_arguments()
    {
        $this->fo(50, 50)->__toString()->shouldReturn("^XA\n^FO50,50\n^XZ");
    }

    function it_accepts_a_single_argument_of_image_for_gf_command(Image $image)
    {
        $image->__toString()->willReturn("FF00");
        $image->widthInBytes()->willReturn(2);
        $image->height()->willReturn(16);

        $this->gf($image)->__toString()->shouldReturn("^XA\n^GFA,32,32,2,FF00\n^XZ");
    }
}
