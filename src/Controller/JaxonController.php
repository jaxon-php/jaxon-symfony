<?php

namespace Jaxon\AjaxBundle\Controller;

use Jaxon\AjaxBundle\Jaxon;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JaxonController extends Controller
{
    /**
     * @var Jaxon       The Jaxon plugin
     */
    protected $jaxon;

    /**
     * The constructor.
     *
     * @param Jaxon     $jaxon      The Jaxon plugin
     */
    public function __construct(Jaxon $jaxon)
    {
        $this->jaxon = $jaxon;
    }

    /**
     * Process a Jaxon request.
     *
     * The HTTP response is automatically sent back to the browser
     *
     * @return void
     */
    public function indexAction()
    {
        $this->jaxon->callback()->before(function ($target, &$bEndRequest) {
            /*
            if($target->isFunction())
            {
                $function = $target->getFunctionName();
            }
            elseif($target->isClass())
            {
                $class = $target->getClassName();
                $method = $target->getMethodName();
                // $instance = $this->jaxon->instance($class);
            }
            */
        });
        $this->jaxon->callback()->after(function ($target, $bEndRequest) {
            /*
            if($target->isFunction())
            {
                $function = $target->getFunctionName();
            }
            elseif($target->isClass())
            {
                $class = $target->getClassName();
                $method = $target->getMethodName();
            }
            */
        });

        if($this->jaxon->canProcessRequest())
        {
            $this->jaxon->processRequest();
            return $this->jaxon->httpResponse();
        }
    }
}
