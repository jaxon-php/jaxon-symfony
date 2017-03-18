<?php

namespace Jaxon\AjaxBundle;

use Symfony\Component\HttpFoundation\Session\Session as SessionManager;

class Session
{
    /**
     * The Symfony session manager
     * 
     * @var object
     */
    protected $xSession = null;

    public function __construct()
    {
        $this->xSession = new SessionManager();
    }

    /**
     * Save data in the session
     *
     * @param string        $sKey                The session key
     * @param string        $xValue              The session value
     * 
     * @return void
     */
    public function set($sKey, $xValue)
    {
        $this->xSession->set($sKey, $xValue);
    }

    /**
     * Check if a session key exists
     *
     * @param string        $sKey                The session key
     * 
     * @return bool             True if the session key exists, else false
     */
    public function has($sKey)
    {
        return $this->xSession->has($sKey);
    }

    /**
     * Get data from the session
     *
     * @param string        $sKey                The session key
     * @param string        $xDefault            The default value
     * 
     * @return mixed|$xDefault             The data under the session key, or the $xDefault parameter
     */
    public function get($sKey, $xDefault = null)
    {
        return $this->has($sKey) ? $this->xSession->get($sKey) : $xDefault;
    }
}
