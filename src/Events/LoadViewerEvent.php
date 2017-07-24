<?php

namespace Greg\AppView\Events;

use Greg\View\Viewer;

class LoadViewerEvent
{
    private $viewer;

    public function __construct(Viewer $viewer)
    {
        $this->viewer = $viewer;
    }

    public function viewer(): Viewer
    {
        return $this->viewer;
    }
}