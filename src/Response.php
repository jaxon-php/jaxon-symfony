<?php

namespace Jaxon\AjaxBundle;

use Symfony\Component\HttpFoundation\Response as HttpResponse;

class Response extends \Jaxon\Response\Response
{
    /**
     * Create a new Response instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Wrap the Jaxon response in a Symfony HTTP response.
     *
     * @param  string  $code
     *
     * @return string  the HTTP response
     */
    public function http($code = '200')
    {
        $response = new HttpResponse();
        $response->headers->set('Content-Type', $this->getContentType());
        $response->setCharset($this->getCharacterEncoding());
        $response->setStatusCode($code);
        $response->setContent($this->getOutput());
        // prints the HTTP headers followed by the content
        $response->send();
    }
}
