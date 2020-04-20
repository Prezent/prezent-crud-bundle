<?php

namespace Prezent\CrudBundle\Tests\Fixture\Functional;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Sander Marechal
 */
class AppKernel extends Kernel
{
    public function __construct($testCase, $env, $debug)
    {
        $this->testCase = $testCase;

        parent::__construct($env, $debug);
    }

    public function registerBundles()
    {
        return [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new \Prezent\GridBundle\PrezentGridBundle(),
            new \Prezent\CrudBundle\PrezentCrudBundle(),
            new \Prezent\CrudBundle\Tests\Fixture\Functional\AppBundle\AppBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yml');

        // Disable logger, it makes testing error pages noisy
        $loader->load(function ($container) {
            $container->register('logger', 'Psr\Log\NullLogger');
        });
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/'.Kernel::VERSION.'/'.$this->testCase.'/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return sys_get_temp_dir().'/'.Kernel::VERSION.'/'.$this->testCase.'/logs';
    }
}

if (!class_exists('AppKernel')) {
    class_alias(AppKernel::class, 'AppKernel');
}
