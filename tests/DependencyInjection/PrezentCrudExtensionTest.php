<?php

namespace Prezent\CrudBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Prezent\CrudBundle\DependencyInjection\PrezentCrudExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Sander Marechal
 */
class PrezentCrudExtensionTest extends TestCase
{
    public function testDefault()
    {
        $container = new ContainerBuilder();

        $extension = new PrezentCrudExtension();
        $extension->load([], $container);

        $this->assertInstanceOf(Definition::class, $container->findDefinition('prezent_crud.template_guesser'));
    }
}
