<?php

namespace Prezent\CrudBundle;

final class CrudEvents
{
    /**
     * The PRE_SUBMIT event occurs before the form is submitted in the add and edit actions
     */
    const PRE_SUBMIT = 'prezent_crud.pre_submit';

    /**
     * The PRE_FLUSH event occurs in the add, edit and delete actions just before the
     * changes are flushed to the persistence layer.
     */
    const PRE_FLUSH = 'prezent_crud.pre_flush';

    /**
     * The POST_FLUSH event occurs in the add, edit and delete actions just after the
     * changes are flushed to the persistence layer.
     */
    const POST_FLUSH = 'prezent_crud.post_flush';

    /**
     * The VALIDATION_FAILED event occurs when a form is submitted but validation fails in
     * the add and edit actions.
     */
    const VALIDATION_FAILED = 'prezent_crud.validation_failed';
}
