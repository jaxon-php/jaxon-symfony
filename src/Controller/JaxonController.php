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
     * Callback before processing a Jaxon request.
     *
     * @param object            $instance               The Jaxon class instance to call
     * @param string            $method                 The Jaxon class method to call
     * @param boolean           $bEndRequest            Whether to end the request or not
     *
     * @return void
     */
    public function beforeRequest($instance, $method, &$bEndRequest)
    {
    }

    /**
     * Callback after processing a Jaxon request.
     *
     * @param object            $instance               The Jaxon class instance called
     * @param string            $method                 The Jaxon class method called
     *
     * @return void
     */
    public function afterRequest($instance, $method)
    {
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
        $this->jaxon->callback()->before(function ($instance, $method, &$bEndRequest) {
            $this->beforeRequest($instance, $method, $bEndRequest);
        });
        $this->jaxon->callback()->after(function ($instance, $method) {
            $this->afterRequest($instance, $method);
        });

        if($this->jaxon->canProcessRequest())
        {
            $this->jaxon->processRequest();
            return $this->jaxon->httpResponse();
        }
    }
}
