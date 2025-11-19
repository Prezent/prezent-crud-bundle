<?php

declare(strict_types=1);

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
    private ?object $object;

    private ?FormInterface $form;

    /**
     * Constructor
     */
    public function __construct(
        Configuration $configuration,
        Request $request,
        ?object $object = null,
        ?FormInterface $form = null
    ) {
        parent::__construct($configuration, $request);

        $this->object = $object;
        $this->form = $form;
    }

    public function getObject(): object
    {
        return $this->object;
    }

    public function getForm(): ?FormInterface
    {
        return $this->form;
    }
}
