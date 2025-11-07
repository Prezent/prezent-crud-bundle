<?php

declare(strict_types=1);

namespace Prezent\CrudBundle\Model;

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
    private string $name;

    private string $action;

    private string $routePrefix;

    private array $routeParameters = [];

    private string $entityClass;

    private string $gridType;

    private string|array $gridTheme;

    private array $gridOptions = [];

    private string $formType;

    private string|array $formTheme;

    private array $formOptions = [];

    private string $defaultSortField = 'id';

    private string $defaultSortOrder = 'ASC';
    
    private string $translationDomain = 'messages';

    private array $templateVariables = [];

    private int $resultsPerPage = 10;

    private EventDispatcherInterface $dispatcher;

    public function __construct(Request $request)
    {
        $this->name = $this->getDefaultName($request);
        $this->action = $this->getDefaultAction($request);
        $this->routePrefix = $this->getDefaultRoutePrefix($request);
        $this->dispatcher = new EventDispatcher();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    public function getRoutePrefix(): string
    {
        return $this->routePrefix;
    }

    public function setRoutePrefix(string $routePrefix): self
    {
        $this->routePrefix = $routePrefix;
        return $this;
    }

    public function getRouteParameters(): array
    {
        return $this->routeParameters;
    }

    public function setRouteParameters(array $routeParameters): self
    {
        $this->routeParameters = $routeParameters;
        return $this;
    }

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function setEntityClass(string $entityClass): self
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getGridType(): string
    {
        return $this->gridType;
    }

    public function setGridType(string $gridType): self
    {
        $this->gridType = $gridType;
        return $this;
    }

    public function getGridTheme(): string|array
    {
        return $this->gridTheme;
    }

    public function setGridTheme(string|array $gridTheme): self
    {
        $this->gridTheme = $gridTheme;
        return $this;
    }

    public function getGridOptions(): array
    {
        return $this->gridOptions;
    }

    public function setGridOptions(array $gridOptions): self
    {
        $this->gridOptions = $gridOptions;
        return $this;
    }

    public function getFormType(): string
    {
        return $this->formType;
    }

    public function setFormType(string $formType): self
    {
        $this->formType = $formType;
        return $this;
    }

    public function getFormTheme(): string|array
    {
        return $this->formTheme;
    }

    public function setFormTheme(string|array $formTheme): self
    {
        $this->formTheme = $formTheme;
        return $this;
    }

    public function getFormOptions(): array
    {
        return $this->formOptions;
    }

    public function setFormOptions(array $formOptions): self
    {
        $this->formOptions = $formOptions;
        return $this;
    }

    public function getDefaultSortField(): string
    {
        return $this->defaultSortField;
    }

    public function setDefaultSortField(string $defaultSortField): self
    {
        $this->defaultSortField = $defaultSortField;
        return $this;
    }

    public function getDefaultSortOrder(): string
    {
        return $this->defaultSortOrder;
    }

    public function setDefaultSortOrder(string $defaultSortOrder): self
    {
        $this->defaultSortOrder = $defaultSortOrder;
        return $this;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(string $translationDomain): self
    {
        $this->translationDomain = $translationDomain;
        return $this;
    }

    public function getTemplateVariables(): array
    {
        return $this->templateVariables;
    }

    public function setTemplateVariables(array $templateVariables = []): self
    {
        $this->templateVariables = $templateVariables;
        return $this;
    }

    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    public function setResultsPerPage($resultsPerPage)
    {
        $this->resultsPerPage = $resultsPerPage;
        return $this;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    public function addEventListener(string $eventName, callable $listener, int $priority = 0): self
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);
        return $this;
    }

    public function addEventSubscriber(EventSubscriberInterface $subscriber): self
    {
        $this->dispatcher->addSubscriber($subscriber);
        return $this;
    }

    /**
     * Validate the configuration
     */
    public function validate(): void
    {
        if (!$this->entityClass) {
            throw new \RuntimeException('You must set the entityClass on the CRUD configuration');
        }

        if (!$this->name) {
            throw new \RuntimeException(
                'Default name could not be found. You must set the name on the CRUD configuration'
            );
        }
    }

    private function getDefaultName(Request $request): ?string
    {
        if (!preg_match('/Controller\\\\([\w\\\\]+)Controller:/', $request->attributes->get('_controller'), $match)) {
            return null;
        }

        return strtolower(str_replace('\\', '_', $match[1]));
    }

    private function getDefaultAction(Request $request): string
    {
        if (!preg_match('/::(\w+)$/', $request->attributes->get('_controller'), $match)) {
            throw new \RuntimeException('Unable to determine controller name');
        }

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', preg_replace('/Action$/', '', $match[1])));
    }

    private function getDefaultRoutePrefix(Request $request): string
    {
        return preg_replace('/[[:alnum:]]+$/', '', $request->attributes->get('_route'));
    }
}
