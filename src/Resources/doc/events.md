Events
======

The `CrudController` dispatches several events that allow you to hook into the default CRUD actions
without having to override its methods. These events are dispatched on a controller's own dispatcher,
not on Symfony's global event dispatcher.

Defined events
--------------

### `CrudEvents::PRE_SUBMIT`

This event is dispatched in the `add` and `edit` actions just before the form is submitted or displayed.
The event handler is passed a `PreSubmitEvent` that contains both the object and the form.

Note that when dispatching this event in the `add` action, the object will usually be `null` unless you
have overridden the `newInstance()` method on the controller. By default new objects are created by the 
`data_class` and `empty_data` options of the Symfony form.

### `CrudEvents::PRE_FLUSH`

This event is dispatched in the `add` and `edit` and `delete` actions just before the data
is flushed to the persistence layer. The event handler is passed a `PreFlushEvent` that contains
the object to be saved.

### `CrudEvents::POST_FLUSH`

This event is dispatched in the `add` and `edit` and `delete` actions just after the data
is flushed to the persistence layer. The event handler is passed a `PostFlushEvent` that contains
the object that was saved and any exception that occured during flushing. Use the `isFlushed()`
method on the event to determine if flushing was succesfull.

Event methods
-------------

### `getAction()`

Returns the current action name

### `getConfiguration()`

Returns the current CRUD configuration.

### `getException()`

Only available on the `PostFlushEvent`. Returns the exception that occurred during flushing, if any.

### `getForm()`

Returns the current form instance. Always `null` during a `delete` action. You can add errors to the form during the
`PreFlushEvent` but not during the `PostFlushEvent`.

### `getObject()`

Returns the current object. Note this may be `null` when a `PreSubmitEvent` is triggered from the `add` action.

### `getRequest()`

Returns the current request.

### `isFlushed()`

Returns a boolean indicating if flushing was a success.

### `setResponse(Response $response)`

Set a response to be returned. Event propagation will be stopped and execution of the rest of the action
will be skipped.

Example
-------

```php
use Prezent\CrudBundle\CrudEvents;
use Prezent\CrudBundle\Event\PostFlushEvent;

class UserController extends CrudController
{
    protected function configure(Request $request, Configuration $config)
    {
        $config
            // ...
            ->addEventListener(CrudEvents::POST_FLUSH, function (PostFlushEvent $event) {
                if ($event->getAction() === 'add' && $event->isFlushed()) {
                    $this->get('app.mailer')->sendWelcomeMail($event->getObject());
                }
            })
        ;
    }
}
```
