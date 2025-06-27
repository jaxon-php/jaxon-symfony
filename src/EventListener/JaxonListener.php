<?php

namespace Jaxon\Symfony\EventListener;

use Jaxon\Symfony\App\Jaxon;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class JaxonListener
{
    public function __construct(private Jaxon $jaxon)
    {}

    public function onKernelController(ControllerEvent $event)
    {
        $this->jaxon->setup();
    }
}
