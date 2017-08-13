<?php

namespace Greg\AppView\Events;

use Greg\View\Viewer;
use PHPUnit\Framework\TestCase;

class LoadViewerEventTest extends TestCase
{
    public function testCanInstantiate()
    {
        $event = new LoadViewerEvent($this->mockViewer());

        $this->assertInstanceOf(LoadViewerEvent::class, $event);
    }

    private function mockViewer()
    {
        return $this->getMockBuilder(Viewer::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
