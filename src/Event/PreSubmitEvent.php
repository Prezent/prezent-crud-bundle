<?php

namespace Prezent\CrudBundle\Event;

use Prezent\CrudBundle\Model\Configuration;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Event dispatched before submitting the form
 *
 * @author Sander Marechal
 */
class PreSubmitEvent extends CrudEvent
{
    /**
     * @var object
     */
    private $object;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * Constructor
     *
     * @param Configuration $configuration
     * @param Request $request
     */
    public function __construct(Configuration $configuration, Request $request, $object, FormInterface $form = null)
    {
        parent::__construct($configuration, $request);

        $this->object = $object;
        $this->form = $form;
    }

    /**
     * Get object
     *
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Get form
     *
     * @return ?FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }
}
