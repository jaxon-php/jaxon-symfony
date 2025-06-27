<?php

namespace Jaxon\Symfony\EventListener;

use Jaxon\Symfony\App\Jaxon;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class JaxonListener
{
    /**
     * @var bool
     */
    private bool $alreadyCalled = false;

    /**
     * @param Jaxon $jaxon
     */
    public function __construct(private Jaxon $jaxon)
    {}

    public function onKernelController(ControllerEvent $event)
    {
        if($this->alreadyCalled)
        {
            return;
        }

        $this->alreadyCalled = true;
        $this->jaxon->setup();
    }
}
