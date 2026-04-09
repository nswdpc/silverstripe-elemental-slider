<?php

declare(strict_types=1);

namespace NSWDPC\Elemental\Tests\Slider;

use NSWDPC\Elemental\Models\Slider\Slide;
use SilverStripe\Dev\SapphireTest;

class SlideTest extends SapphireTest
{

    protected $usesDatabase = true;

    public function testSlideWidth(): void
    {
        $slide = Slide::create([
            'Title' => 'Slide test width',
            'Width' => 640,
            'Height' => 480
        ]);
        $slide->write();
        $slide->publishSingle();

        $width = $slide->getThumbWidth();
        $this->assertEquals(640,$width);
    }

    public function testSlideHeight(): void
    {
        $slide = Slide::create([
            'Title' => 'Slide test height',
            'Width' => 640,
            'Height' => 480
        ]);
        $slide->write();
        $slide->publishSingle();

        $height = $slide->getThumbHeight();
        $this->assertEquals(480,$height);
    }

    public function testSlideDimensions(): void
    {
        $slide = Slide::create([
            'Title' => 'Slide test dimensions',
            'Width' => 960,
            'Height' => 600
        ]);
        $slide->write();
        $slide->publishSingle();

        $height = $slide->getThumbHeight();
        $this->assertEquals(600,$height);
        $width = $slide->getThumbWidth();
        $this->assertEquals(960,$width);
    }

        public function testSlideDefaultDimensions(): void
        {
            $slide = Slide::create([
                'Title' => 'Slide test default dimensions',
            ]);
            $slide->write();
            $slide->publishSingle();

            $height = $slide->getThumbHeight();
            $this->assertEquals(Slide::config()->get('default_thumb_height'),$height);
            $width = $slide->getThumbWidth();
            $this->assertEquals(Slide::config()->get('default_thumb_width'),$width);
        }
}
