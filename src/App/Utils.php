<?php

namespace Jaxon\Symfony\App;

use Symfony\Component\HttpKernel\Kernel;

use function version_compare;

class Utils
{
    /**
     * Get the name of the session service
     *
     * @return string
     */
    public function getSessionService()
    {
        // Starting from version 5.3, the session service is deprecated.
        // The session manager is read in the request service.
        return version_compare(Kernel::VERSION, '5.3.0', '<') ? 'session' : 'request_stack';
    }
}
