<?php

namespace Jaxon\AjaxBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JaxonController extends Controller
{
    /**
     * Process the Jaxon request
     */
    public function indexAction()
    {
        $jaxon = $this->get('jaxon.ajax');
        if($jaxon->canProcessRequest())
        {
            $jaxon->processRequest();
        }
    }
}
