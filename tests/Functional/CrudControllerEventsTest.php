<?php

namespace Prezent\CrudBundle\Tests\Functional;

use Prezent\CrudBundle\CrudEvents;
use Prezent\CrudBundle\Event\PostFlushEvent;
use Prezent\CrudBundle\Event\PreFlushEvent;
use Prezent\CrudBundle\Event\PreSubmitEvent;
use Prezent\CrudBundle\Event\ValidationFailedEvent;
use Prezent\CrudBundle\Tests\Fixture\Functional\AppBundle\Controller\ProductController;
use Prezent\CrudBundle\Tests\Fixture\Functional\AppFixtures;
use Prezent\CrudBundle\Tests\Fixture\InvokableListener;

/**
 * @author Sander Marechal
 */
class CrudControllerEventsTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        self::createDatabase($this->client);
        ProductController::setConfigurator(null);
    }

    public function testAddEvents()
    {
        // Loading the form only triggers PreSubmit
        ProductController::setConfigurator(function ($config) {
            $config
                ->addEventListener(CrudEvents::PRE_SUBMIT, $this->createListener(PreSubmitEvent::class, 1))
                ->addEventListener(CrudEvents::PRE_FLUSH, $this->createListener(PreFlushEvent::class, 0))
                ->addEventListener(CrudEvents::POST_FLUSH, $this->createListener(PostFlushEvent::class, 0))
                ->addEventListener(CrudEvents::VALIDATION_FAILED, $this->createListener(ValidationFailedEvent::class, 0))
            ;
        });

        $crawler = $this->client->request('GET', '/product/add');

        $form = $crawler->selectButton('form.product.submit')->form();
        $form['product_form[name]'] = 'quu';
        $form['product_form[valid]'] = '1';

        // Saving the form triggers all events
        ProductController::setConfigurator(function ($config) {
            $config
                ->addEventListener(CrudEvents::PRE_SUBMIT, $this->createListener(PreSubmitEvent::class, 1))
                ->addEventListener(CrudEvents::PRE_FLUSH, $this->createListener(PreFlushEvent::class, 1))
                ->addEventListener(CrudEvents::POST_FLUSH, $this->createListener(PostFlushEvent::class, 1))
                ->addEventListener(CrudEvents::VALIDATION_FAILED, $this->createListener(ValidationFailedEvent::class, 0))
            ;
        });

        $crawler = $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testEditEvents()
    {
        self::loadFixtures($this->client, new AppFixtures(1));

        // Loading the form only triggers PreSubmit
        ProductController::setConfigurator(function ($config) {
            $config
                ->addEventListener(CrudEvents::PRE_SUBMIT, $this->createListener(PreSubmitEvent::class, 1))
                ->addEventListener(CrudEvents::PRE_FLUSH, $this->createListener(PreFlushEvent::class, 0))
                ->addEventListener(CrudEvents::POST_FLUSH, $this->createListener(PostFlushEvent::class, 0))
                ->addEventListener(CrudEvents::VALIDATION_FAILED, $this->createListener(ValidationFailedEvent::class, 0))
            ;
        });

        $crawler = $this->client->request('GET', '/product/edit/1');

        $form = $crawler->selectButton('form.product.submit')->form();
        $form['product_form[name]'] = 'quu';
        $form['product_form[valid]'] = '1';

        // Saving the form triggers all events
        ProductController::setConfigurator(function ($config) {
            $config
                ->addEventListener(CrudEvents::PRE_SUBMIT, $this->createListener(PreSubmitEvent::class, 1))
                ->addEventListener(CrudEvents::PRE_FLUSH, $this->createListener(PreFlushEvent::class, 1))
                ->addEventListener(CrudEvents::POST_FLUSH, $this->createListener(PostFlushEvent::class, 1))
                ->addEventListener(CrudEvents::VALIDATION_FAILED, $this->createListener(ValidationFailedEvent::class, 0))
            ;
        });

        $crawler = $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testDeleteEvents()
    {
        self::loadFixtures($this->client, new AppFixtures(1));

        // Deleting only triggers pre/post flush events
        ProductController::setConfigurator(function ($config) {
            $config
                ->addEventListener(CrudEvents::PRE_SUBMIT, $this->createListener(PreSubmitEvent::class, 0))
                ->addEventListener(CrudEvents::PRE_FLUSH, $this->createListener(PreFlushEvent::class, 1))
                ->addEventListener(CrudEvents::POST_FLUSH, $this->createListener(PostFlushEvent::class, 1))
                ->addEventListener(CrudEvents::VALIDATION_FAILED, $this->createListener(ValidationFailedEvent::class, 0))
            ;
        });

        $crawler = $this->client->request('GET', '/product/delete/1');

        $this->assertTrue($this->client->getResponse()->isRedirect());
    }

    public function testAddValidationFailedEvent()
    {
        self::loadFixtures($this->client, new AppFixtures(1));
        $crawler = $this->client->request('GET', '/product/add');

        $form = $crawler->selectButton('form.product.submit')->form();
        $form->remove('product_form[valid]');

        ProductController::setConfigurator(function ($config) {
            $config
                ->addEventListener(CrudEvents::PRE_SUBMIT, $this->createListener(PreSubmitEvent::class, 1))
                ->addEventListener(CrudEvents::PRE_FLUSH, $this->createListener(PreFlushEvent::class, 0))
                ->addEventListener(CrudEvents::POST_FLUSH, $this->createListener(PostFlushEvent::class, 0))
                ->addEventListener(CrudEvents::VALIDATION_FAILED, $this->createListener(ValidationFailedEvent::class, 1))
            ;
        });

        $crawler = $this->client->submit($form);

        $this->assertFalse($this->client->getResponse()->isRedirect());
    }

    public function testEditValidationFailedEvent()
    {
        self::loadFixtures($this->client, new AppFixtures(1));
        $crawler = $this->client->request('GET', '/product/edit/1');

        $form = $crawler->selectButton('form.product.submit')->form();
        $form->remove('product_form[valid]');

        ProductController::setConfigurator(function ($config) {
            $config
                ->addEventListener(CrudEvents::PRE_SUBMIT, $this->createListener(PreSubmitEvent::class, 1))
                ->addEventListener(CrudEvents::PRE_FLUSH, $this->createListener(PreFlushEvent::class, 0))
                ->addEventListener(CrudEvents::POST_FLUSH, $this->createListener(PostFlushEvent::class, 0))
                ->addEventListener(CrudEvents::VALIDATION_FAILED, $this->createListener(ValidationFailedEvent::class, 1))
            ;
        });

        $crawler = $this->client->submit($form);

        $this->assertFalse($this->client->getResponse()->isRedirect());
    }

    private function createListener($eventClass, $count)
    {
        $listener = $this->createPartialMock(InvokableListener::class, ['__invoke']);
        $listener->expects($this->exactly($count))
            ->method('__invoke')
            ->with($this->isInstanceOf($eventClass));

        return $listener;
    }
}
