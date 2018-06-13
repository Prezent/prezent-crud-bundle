<?php

namespace Prezent\CrudBundle\Tests\Event;

use PHPUnit\Framework\TestCase;
use Prezent\CrudBundle\Event\CrudEvent;
use Prezent\CrudBundle\Model\Configuration;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Sander Marechal
 */
class CrudEventTest extends TestCase
{
    public function testStopPropagationOnResponse()
    {
        $event = new CrudEvent(
            $this->createMock(Configuration::class),
            $this->createMock(Request::class)
        );

        $this->assertFalse($event->isPropagationStopped());

        $event->setResponse(new Response());
        $this->assertTrue($event->isPropagationStopped());
    }
}
