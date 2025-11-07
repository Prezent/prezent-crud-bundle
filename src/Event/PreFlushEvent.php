<?php

declare(strict_types=1);

namespace Prezent\CrudBundle\Event;

use Prezent\CrudBundle\Model\Configuration;
use Symfony\Component\HttpFoundation\Request;

/**
 * Event dispatched before flushing the changes
 *
 * @author Sander Marechal
 */
class PreFlushEvent extends PreSubmitEvent
{
}
