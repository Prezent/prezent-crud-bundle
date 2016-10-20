<?php

namespace Prezent\CrudBundle\Templating;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Doctrine\Common\Util\ClassUtils;

/**
 * The TemplateGuesser class handles the guessing of template name based on controller.
 * Based off from Sensio\FrameworkExtraBundle\Templating\TemplateGuesser
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Sander Marechal
 */
class TemplateGuesser
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var string[]
     */
    private $controllerPatterns;

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel             A KernelInterface instance
     * @param string[]        $controllerPatterns Regexps extracting the controller name from its FQN.
     */
    public function __construct(KernelInterface $kernel, array $controllerPatterns = [])
    {
        $controllerPatterns[] = '/Controller\\\(.+)Controller$/';

        $this->kernel = $kernel;
        $this->controllerPatterns = $controllerPatterns;
    }

    /**
     * Guess multiple possible template names based on the controller
     *
     * @param callable $controller An array storing the controller object and action method
     * @param Request  $request    A Request instance
     * @param string   $engine
     *
     * @return TemplateReference[] Array of template references
     *
     * @throws \InvalidArgumentException
     */
    public function guessTemplateNames($controller, Request $request, $engine = 'twig')
    {
        if (is_object($controller) && method_exists($controller, '__invoke')) {
            $controller = [$controller, '__invoke'];
        } elseif (!is_array($controller)) {
            throw new \InvalidArgumentException(sprintf('First argument of %s must be an array callable or an object defining the magic method __invoke. "%s" given.', __METHOD__, gettype($controller)));
        }

        if (!is_string($controller[0])) {
            $controller[0] = class_exists('Doctrine\Common\Util\ClassUtils')
                ? ClassUtils::getClass($controller[0])
                : get_class($controller[0]);
        }

        $reflClass = new \ReflectionClass($controller[0]);
        $templates = [];

        do {
            $controller[0] = $reflClass->getName();

            if ($template = $this->guessTemplateName($controller, $request, $engine)) {
                $templates[] = $template;
            }

            $reflClass = $reflClass->getParentClass();
        } while ($reflClass);

        return $templates;
    }

    /**
     * Guesses and returns the template name to render based on the controller
     * and action names.
     *
     * @param callable $controller An array storing the controller classname and action method
     * @param Request  $request    A Request instance
     * @param string   $engine
     *
     * @return TemplateReference template reference
     *
     * @throws \InvalidArgumentException
     */
    private function guessTemplateName($controller, Request $request, $engine)
    {
        $matchController = null;

        foreach ($this->controllerPatterns as $pattern) {
            if (preg_match($pattern, $controller[0], $tempMatch)) {
                $matchController = $tempMatch;
                break;
            }
        }

        if (null === $matchController) {
            return;
        }

        if ($controller[1] === '__invoke') {
            $matchAction = $matchController;
            $matchController = null;
        } elseif (!preg_match('/^(.+)Action$/', $controller[1], $matchAction)) {
            $matchAction = [null, $controller[1]];
        }

        $bundle = $this->getBundleForClass($controller[0]);

        if ($bundle) {
            $bundleName = $bundle->getName();
        } else {
            $bundleName = null;
        }

        return new TemplateReference($bundleName, $matchController[1], $matchAction[1], $request->getRequestFormat(), $engine);
    }

    /**
     * Returns the Bundle instance in which the given class name is located.
     *
     * @param string $class A fully qualified controller class name
     *
     * @return Bundle|null $bundle A Bundle instance
     */
    private function getBundleForClass($class)
    {
        $reflectionClass = new \ReflectionClass($class);
        $bundles = $this->kernel->getBundles();

        do {
            $namespace = $reflectionClass->getNamespaceName();
            foreach ($bundles as $bundle) {
                if (0 === strpos($namespace, $bundle->getNamespace())) {
                    return $bundle;
                }
            }
            $reflectionClass = $reflectionClass->getParentClass();
        } while ($reflectionClass);
    }
}
