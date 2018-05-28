<?php

namespace Prezent\CrudBundle\Event;

use Prezent\CrudBundle\Model\Configuration;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base event class
 *
 * @author Sander Marechal
 */
class CrudEvent extends Event
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * Constructor
     *
     * @param Configuration $configuration
     * @param Request $request
     */
    public function __construct(Configuration $configuration, Request $request)
    {
        $this->configuration = $configuration;
        $this->request = $request;
    }

    /**
     * Get configuration
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Get the current action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->configuration->getAction();
    }

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Check if there is a response
     *
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response != null;
    }

    /**
     * Get response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * Set response
     *
     * @param Response $response
     * @return self
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        $this->stopPropagation();

        return $this;
    }
}
