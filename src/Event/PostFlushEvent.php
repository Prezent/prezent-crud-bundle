<?php

namespace Prezent\CrudBundle\Event;

/**
 * Event dispatched after flushing the changes
 *
 * @author Sander Marechal
 */
class PostFlushEvent extends PreFlushEvent
{
    /**
     * @var |Exception
     */
    private $exception;

    /**
     * Were all changes flushed?
     *
     * @return bool
     */
    public function isFlushed()
    {
        return $this->exception === null;
    }

    /**
     * Get exception
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
    
    /**
     * Set exception
     *
     * @param \Exception $exception
     * @return self
     */
    public function setException($exception)
    {
        $this->exception = $exception;
        return $this;
    }
}
