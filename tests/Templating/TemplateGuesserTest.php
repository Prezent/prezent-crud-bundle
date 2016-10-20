<?php

namespace Prezent\CrudBundle\Tests;

use Prezent\CrudBundle\PrezentCrudBundle;
use Prezent\CrudBundle\Templating\TemplateGuesser;
use Prezent\CrudBundle\Tests\Fixture\Controller\ProductController;
use Prezent\CrudBundle\Tests\Fixture\TestBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class TemplateGuesserTest extends \PHPUnit_Framework_TestCase
{
    public function testInheritance()
    {
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->getMock()
        ;

        $kernel
            ->expects($this->any())
            ->method('getBundles')
            ->will($this->returnValue([
                'TestBundle' => new TestBundle(),
                'PrezentCrudBundle' => new PrezentCrudBundle(),
            ]))
        ;

        $guesser = new TemplateGuesser($kernel);
        $templates = $guesser->guessTemplateNames([ProductController::class, 'index'], new Request(), 'twig');

        $this->assertCount(3, $templates);
        $this->assertEquals('TestBundle:Product:index.html.twig', $templates[0]->getLogicalName());
        $this->assertEquals('TestBundle:Admin:index.html.twig', $templates[1]->getLogicalName());
        $this->assertEquals('PrezentCrudBundle:Crud:index.html.twig', $templates[2]->getLogicalName());
    }
}
