<?php

namespace Prezent\CrudBundle\Model;

use Prezent\CrudBundle\Controller\CrudController;

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
    private $routePrefix;

    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var string
     */
    private $gridType;

    /**
     * @var string
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
     * @var string
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
     * Constructor
     *
     * @param CrudController $controller
     */
    public function __construct(CrudController $controller)
    {
        $this->name = $this->getDefaultName($controller);
        $this->routePrefix = $this->getDefaultRoutePrefix($controller);
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
     * @return string
     */
    public function getGridTheme()
    {
        return $this->gridTheme;
    }
    
    /**
     * Set gridTheme
     *
     * @param string $gridTheme
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
     * @return string
     */
    public function getFormTheme()
    {
        return $this->formTheme;
    }
    
    /**
     * Setter for formTheme
     *
     * @param string $formTheme
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
     * Validate the configuration
     *
     * @return void
     */
    public function validate()
    {
        if (!$this->entityClass) {
            throw new \RuntimeException('You must set the entityClass on the CRUD configuration');
        }

        if (!$this->formType) {
            throw new \RuntimeException('You must set the formType on the CRUD configuration');
        }

        if (!$this->gridType) {
            throw new \RuntimeException('You must set the gridType on the CRUD configuration');
        }
    }

    /**
     * Get the default controller name
     *
     * @return string
     */
    private function getDefaultName(CrudController $controller)
    {
        if (!preg_match('/(\w+)Controller$/', get_class($controller), $match)) {
            throw new \RuntimeException('Unable to determine controller name');
        }

        return strtolower($match[1]);
    }

    /**
     * Get the default route prefix
     *
     * @return string
     */
    private function getDefaultRoutePrefix(CrudController $controller)
    {
        $name = strtolower(str_replace('\\', '_', get_class($controller)));

        return preg_replace(['/(bundle|controller)_?/', '/__/'], ['_', '_'], $name);
    }
}
