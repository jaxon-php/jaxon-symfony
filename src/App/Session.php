<?php

namespace Jaxon\Symfony\App;

use Jaxon\App\Session\SessionInterface;

class Session implements SessionInterface
{
    /**
     * The Symfony session manager
     *
     * @var mixed
     */
    protected $xSession = null;

    public function __construct($session)
    {
        $this->xSession = $session;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->xSession->getId();
    }

    /**
     * @inheritDoc
     */
    public function newId(bool $bDeleteData = false): void
    {
        $this->xSession->migrate($bDeleteData);
    }

    /**
     * @inheritDoc
     */
    public function set(string $sKey, mixed $xValue): void
    {
        $this->xSession->set($sKey, $xValue);
    }

    /**
     * @inheritDoc
     */
    public function has(string $sKey): bool
    {
        return $this->xSession->has($sKey);
    }

    /**
     * @inheritDoc
     */
    public function get(string $sKey, mixed $xDefault = null): mixed
    {
        return $this->xSession->get($sKey, $xDefault);
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->xSession->all();
    }

    /**
     * @inheritDoc
     */
    public function delete(string $sKey): void
    {
        $this->xSession->remove($sKey);
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->xSession->clear();
    }
}
