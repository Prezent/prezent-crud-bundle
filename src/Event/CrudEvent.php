<?php

declare(strict_types=1);

namespace Prezent\CrudBundle\Event;

use Prezent\CrudBundle\Model\Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Base event class
 *
 * @author Sander Marechal
 */
class CrudEvent extends Event
{
    private ?Response $response = null;

    public function __construct(
        private readonly Configuration $configuration,
        private readonly Request $request
    ) {
    }

    /**
     * Get configuration
     */
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * Get the current action
     */
    public function getAction(): string
    {
        return $this->configuration->getAction();
    }

    /**
     * Get request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Check if there is a response
     */
    public function hasResponse(): bool
    {
        return $this->response != null;
    }

    /**
     * Get response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Set response
     */
    public function setResponse(Response $response): self
    {
        $this->response = $response;
        $this->stopPropagation();

        return $this;
    }
}
