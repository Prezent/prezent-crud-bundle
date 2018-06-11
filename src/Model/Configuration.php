<?php

namespace Prezent\CrudBundle\Model;

use Prezent\CrudBundle\Controller\CrudController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Crud configuration
 *
 * @author Sander Marechal
 */
class Configuration
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $routePrefix;

    /**
     * @var array
     */
    private $routeParameters = [];

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var string
     */
    private $gridType;

    /**
     * @var string|array
     */
    private $gridTheme;

    /**
     * @var array
     */
    private $gridOptions = [];

    /**
     * @var string
     */
    private $formType;

    /**
     * @var string|array
     */
    private $formTheme;

    /**
     * @var array
     */
    private $formOptions = [];

    /**
     * @var string
     */
    private $defaultSortField = 'id';

    /**
     * @var string
     */
    private $defaultSortOrder = 'ASC';

    /**
     * @var string
     */
    private $translationDomain = 'messages';

    /**
     * @var array
     */
    private $templateVariables = [];

    /**
     * @var int
     */
    private $resultsPerPage = 10;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Constructor
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->name = $this->getDefaultName($request);
        $this->action = $this->getDefaultAction($request);
        $this->routePrefix = $this->getDefaultRoutePrefix($request);
        $this->dispatcher = new EventDispatcher();
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return self
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Get routePrefix
     *
     * @return string
     */
    public function getRoutePrefix()
    {
        return $this->routePrefix;
    }

    /**
     * Set routePrefix
     *
     * @param string $routePrefix
     * @return self
     */
    public function setRoutePrefix($routePrefix)
    {
        $this->routePrefix = $routePrefix;
        return $this;
    }

    /**
     * Get routeParameters
     *
     * @return array
     */
    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    /**
     * Set routeParameters
     *
     * @param array $routeParameters
     * @return self
     */
    public function setRouteParameters(array $routeParameters)
    {
        $this->routeParameters = $routeParameters;
        return $this;
    }

    /**
     * Getter for class
     *
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set entityClass
     *
     * @param string $entityClass
     * @return self
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * Get gridType
     *
     * @return string
     */
    public function getGridType()
    {
        return $this->gridType;
    }

    /**
     * Set gridType
     *
     * @param string $gridType
     * @return self
     */
    public function setGridType($gridType)
    {
        $this->gridType = $gridType;
        return $this;
    }

    /**
     * Get gridTheme
     *
     * @return string|array
     */
    public function getGridTheme()
    {
        return $this->gridTheme;
    }

    /**
     * Set gridTheme
     *
     * @param string|array $gridTheme
     * @return self
     */
    public function setGridTheme($gridTheme)
    {
        $this->gridTheme = $gridTheme;
        return $this;
    }

    /**
     * Getter for gridOptions
     *
     * @return array
     */
    public function getGridOptions()
    {
        return $this->gridOptions;
    }

    /**
     * Setter for gridOptions
     *
     * @param array $gridOptions
     * @return self
     */
    public function setGridOptions(array $gridOptions)
    {
        $this->gridOptions = $gridOptions;
        return $this;
    }

    /**
     * Get formType
     *
     * @return string
     */
    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * Set formType
     *
     * @param string $formType
     * @return self
     */
    public function setFormType($formType)
    {
        $this->formType = $formType;
        return $this;
    }

    /**
     * Getter for formTheme
     *
     * @return string|array
     */
    public function getFormTheme()
    {
        return $this->formTheme;
    }

    /**
     * Setter for formTheme
     *
     * @param string|array $formTheme
     * @return self
     */
    public function setFormTheme($formTheme)
    {
        $this->formTheme = $formTheme;
        return $this;
    }

    /**
     * Getter for formOptions
     *
     * @return array
     */
    public function getFormOptions()
    {
        return $this->formOptions;
    }

    /**
     * Setter for formOptions
     *
     * @param array $formOptions
     * @return self
     */
    public function setFormOptions(array $formOptions)
    {
        $this->formOptions = $formOptions;
        return $this;
    }

    /**
     * Getter for defaultSortField
     *
     * @return string
     */
    public function getDefaultSortField()
    {
        return $this->defaultSortField;
    }

    /**
     * Setter for defaultSortField
     *
     * @param string $defaultSortField
     * @return self
     */
    public function setDefaultSortField($defaultSortField)
    {
        $this->defaultSortField = $defaultSortField;
        return $this;
    }

    /**
     * Getter for defaultSortOrder
     *
     * @return string
     */
    public function getDefaultSortOrder()
    {
        return $this->defaultSortOrder;
    }

    /**
     * Setter for defaultSortOrder
     *
     * @param string $defaultSortOrder
     * @return self
     */
    public function setDefaultSortOrder($defaultSortOrder)
    {
        $this->defaultSortOrder = $defaultSortOrder;
        return $this;
    }

    /**
     * Get translationDomain
     *
     * @return string
     */
    public function getTranslationDomain()
    {
        return $this->translationDomain;
    }

    /**
     * Set translationDomain
     *
     * @param string $translationDomain
     * @return self
     */
    public function setTranslationDomain($translationDomain)
    {
        $this->translationDomain = $translationDomain;
        return $this;
    }

    /**
     * Get templateVariables
     *
     * @return array
     */
    public function getTemplateVariables()
    {
        return $this->templateVariables;
    }

    /**
     * Set templateVariables
     *
     * @param array $templateVariables
     * @return self
     */
    public function setTemplateVariables(array $templateVariables = [])
    {
        $this->templateVariables = $templateVariables;
        return $this;
    }

    /**
     * @return int
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    /**
     * @param int $resultsPerPage
     *
     * @return self
     */
    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;
        return $this;
    }

    /**
     * Get dispatcher
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * Add an event listener
     *
     * @param string $eventName
     * @param callable $listener
     * @param int $priority
     * @return self
     */
    public function addEventListener($eventName, $listener, $priority = 0)
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);
        return $this;
    }

    /**
     * Add an event subscriber
     *
     * @param EventSubscriberInterface $subscriber
     * @return self
     */
    public function addEventSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);
        return $this;
    }

    /**
     * Validate the configuration
     *
     * @return void
     */
    public function validate()
    {
        if (!$this->entityClass) {
            throw new \RuntimeException('You must set the entityClass on the CRUD configuration');
        }
    }

    /**
     * Get the default controller name
     *
     * @param Request $request
     * @return string
     */
    private function getDefaultName(Request $request)
    {
        if (!preg_match('/Bundle\\\\Controller\\\\([\w\\\\]+)Controller:/', $request->attributes->get('_controller'), $match)) {
            throw new \RuntimeException('Unable to determine controller name');
        }

        return strtolower(str_replace('\\', '_', $match[1]));
    }

    /**
     * Get the default controller action
     *
     * @param Request $request
     * @return string
     */
    private function getDefaultAction(Request $request)
    {
        if (!preg_match('/(\w+)Action$/', $request->attributes->get('_controller'), $match)) {
            throw new \RuntimeException('Unable to determine controller name');
        }

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $match[1]));
    }

    /**
     * Get the default route prefix
     *
     * @param Request $request
     * @return string
     */
    private function getDefaultRoutePrefix(Request $request)
    {
        return preg_replace('/[[:alnum:]]+$/', '', $request->attributes->get('_route'));
    }
}
