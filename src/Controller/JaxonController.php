<?php

namespace Jaxon\Symfony\Controller;

use Jaxon\Symfony\App\Jaxon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class JaxonController extends AbstractController
{
    public function __invoke(Jaxon $jaxon): Response
    {
        return !$jaxon->canProcessRequest() ?
            new Response() : // Todo: return an error message
            $jaxon->processRequest();
    }
}
