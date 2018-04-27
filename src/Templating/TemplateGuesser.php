<?php

namespace Prezent\CrudBundle\Templating;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @return string[] Array of template references
     *
     * @throws \InvalidArgumentException
     */
    public function guessTemplateNames($controller, Request $request)
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

            if ($template = $this->guessTemplateName($controller, $request)) {
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
     * @return TemplateReference|null template reference
     *
     * @throws \InvalidArgumentException
     */
    private function guessTemplateName($controller, Request $request)
    {
        $matchController = null;

        foreach ($this->controllerPatterns as $pattern) {
            if (preg_match($pattern, $controller[0], $tempMatch)) {
                $matchController = $tempMatch;
                $matchController = str_replace('\\', '/', strtolower(preg_replace('/([a-z\d])([A-Z])/', '\\1_\\2', $tempMatch[1])));
                break;
            }
        }

        if (null === $matchController) {
            return null;
        }

        if ($controller[1] === '__invoke') {
            $matchAction = $matchController;
            $matchController = null;
        } elseif (!preg_match('/^(.+)Action$/', $controller[1], $matchAction)) {
            $matchAction = preg_replace('/Action$/', '', $controller[1]);
        }

        $matchAction = strtolower(preg_replace('/([a-z\d])([A-Z])/', '\\1_\\2', $matchAction));
        $bundleName = $this->getBundleForClass($controller[0]);

        return sprintf(($bundleName ? '@'.$bundleName.'/' : '').$matchController.($matchController ? '/' : '').$matchAction.'.'.$request->getRequestFormat().'.twig');
    }

    /**
     * Returns the Bundle instance in which the given class name is located.
     *
     * @param string $class A fully qualified controller class name
     *
     * @return BundleInterface|null $bundle A Bundle instance
     */
    private function getBundleForClass($class)
    {
        $reflectionClass = new \ReflectionClass($class);
        $bundles = $this->kernel->getBundles();

        $namespace = $reflectionClass->getNamespaceName();
        foreach ($bundles as $bundle) {
            if ('Symfony\Bundle\FrameworkBundle' === $bundle->getNamespace()) {
                continue;
            }
            if (0 === strpos($namespace, $bundle->getNamespace())) {
                return preg_replace('/Bundle$/', '', $bundle->getName());
            }
        }

        return null;
    }
}
