<?php

namespace Prezent\CrudBundle\Tests\Model;

use Prezent\CrudBundle\Model\Configuration;
use Prezent\CrudBundle\Tests\Fixture\Controller\AdminController;
use Symfony\Component\HttpFoundation\Request;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $request = new Request();
        $request->attributes->set('_controller', 'ProductController::indexAction');
        $request->attributes->set('_route', 'product_index');

        $configuration = new Configuration($request);

        $this->assertEquals('product', $configuration->getName());
        $this->assertEquals('index', $configuration->getAction());
        $this->assertEquals('product_', $configuration->getRoutePrefix());
    }

    public function testLongNames()
    {
        $request = new Request();
        $request->attributes->set('_controller', 'Vendor\MyBundle\Controller\ProductTypeController::camelCasedAction');
        $request->attributes->set('_route', 'vendor_my_producttype_index');

        $configuration = new Configuration($request);

        $this->assertEquals('producttype', $configuration->getName());
        $this->assertEquals('camel_cased', $configuration->getAction());
        $this->assertEquals('vendor_my_producttype_', $configuration->getRoutePrefix());
    }
}
