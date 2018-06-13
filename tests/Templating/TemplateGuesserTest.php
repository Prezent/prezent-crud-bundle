<?php

namespace Prezent\CrudBundle\Tests;

use PHPUnit\Framework\TestCase;
use Prezent\CrudBundle\PrezentCrudBundle;
use Prezent\CrudBundle\Templating\TemplateGuesser;
use Prezent\CrudBundle\Tests\Fixture\InheritingBundle\Controller\ProductController;
use Prezent\CrudBundle\Tests\Fixture\InheritingBundle\InheritingBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class TemplateGuesserTest extends TestCase
{
    public function testInheritance()
    {
        $kernel = $this
            ->getMockBuilder(KernelInterface::class)
            ->getMock()
        ;

        $kernel
            ->method('getBundles')
            ->will($this->returnValue([
                'TestBundle' => new InheritingBundle(),
                'PrezentCrudBundle' => new PrezentCrudBundle(),
            ]))
        ;

        $guesser = new TemplateGuesser($kernel);
        $templates = $guesser->guessTemplateNames([ProductController::class, 'index'], new Request(), 'twig');

        $this->assertCount(3, $templates);
        $this->assertEquals('@Inheriting/product/index.html.twig', $templates[0]);
        $this->assertEquals('@Inheriting/admin/index.html.twig', $templates[1]);
        $this->assertEquals('@PrezentCrud/crud/index.html.twig', $templates[2]);
    }
}
