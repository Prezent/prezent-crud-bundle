<?php

namespace Prezent\CrudBundle\Event;

use Prezent\CrudBundle\Model\Configuration;
use Symfony\Component\HttpFoundation\Request;

/**
 * Event dispatched before flushing the changes
 *
 * @author Sander Marechal
 */
class PreFlushEvent extends CrudEvent
{
    /**
     * @var object
     */
    private $object;

    /**
     * Constructor
     *
     * @param Configuration $configuration
     * @param Request $request
     */
    public function __construct(Configuration $configuration, Request $request, $object)
    {
        parent::__construct($configuration, $request);

        $this->object = $object;
    }

    /**
     * Get object
     *
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }
}
