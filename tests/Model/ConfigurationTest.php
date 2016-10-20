<?php

namespace Prezent\CrudBundle\Tests\Model;

use Prezent\CrudBundle\Model\Configuration;
use Prezent\CrudBundle\Tests\Fixture\Controller\AdminController;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $controller = $this
            ->getMockBuilder(AdminController::class)
            ->setMockClassName('ProductController')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $configuration = new Configuration($controller);

        $this->assertEquals('product', $configuration->getName());
        $this->assertEquals('product_', $configuration->getRoutePrefix());
    }

    public function testLongNames()
    {
        $controller = $this
            ->getMockBuilder(AdminController::class)
            ->setMockClassName('ProductTypeController')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $configuration = new Configuration($controller);

        $this->assertEquals('producttype', $configuration->getName());
        $this->assertEquals('producttype_', $configuration->getRoutePrefix());
    }
}
