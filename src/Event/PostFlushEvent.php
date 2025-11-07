<?php

declare(strict_types=1);

namespace Prezent\CrudBundle\Event;

/**
 * Event dispatched after flushing the changes
 *
 * @author Sander Marechal
 */
class PostFlushEvent extends PreFlushEvent
{
    private \Exception $exception;

    /**
     * Were all changes flushed?
     */
    public function isFlushed(): bool
    {
        return $this->exception === null;
    }

    /**
     * Get exception
     */
    public function getException(): \Exception
    {
        return $this->exception;
    }

    /**
     * Set exception
     */
    public function setException(\Exception $exception): self
    {
        $this->exception = $exception;
        return $this;
    }
}
