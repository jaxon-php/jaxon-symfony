<?php

namespace Jaxon\Symfony\Controller;

use Jaxon\Symfony\App\Jaxon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JaxonController extends AbstractController
{
    public function __invoke(Jaxon $jaxon)
    {
        if(!$jaxon->canProcessRequest())
        {
            return; // Todo: return an error message
        }

        return $jaxon->processRequest();
    }
}
