Extending the base controller
=============================

You are free to exend the base CrudController in any way that you require. There
are several helper methods to help you create your own actions, and a few methods
that you can override to add extra behaviour to the built-in actions.


Helper methods
--------------

### `getConfiguration()`

Get the CRUD configuration model.


### `findObject(mixed $id)`

Find an object by its ID. The ID value is passed on to the Doctrine repository `find` method.
If an object is not found, a `NotFoundHttpException` will be thrown.


### `getTemplate(Request $request, $action)`

Find the template to use for rendering an action. The template type (e.g. html or json) is determined
from the current request. This method takes your controller inheritance into account. For example, say
that you have the following controllers defined:

```php
namespace AppBundle\Controller;

use Prezent\CrudBundle\Controller\CrudController;

abstract class AdminController extends CrudController
{
    // ...
}

class ProductController extends AdminController
{
    // ...
}
```

Now, if you want to render a custom `view` action, you can use `getTemplate` like this:

```php
public function viewAction(Request $request)
{
    return $this->render($this->getTemplate($request, 'view'), [
        // data
    ]);
}
```

It would try and find the following template, rendering the first one that exists:

1. `AppBundle:Product:view.html.twig`
2. `AppBundle:Admin:view.html.twig`
3. `PrezentCrudBundle:Crud:view.html.twig`


### `getObjectManager()`

get the Doctrine object manager responsible for the configured class.


### `getRepository()`

Get the Doctrine object repository for the configured class.


Creating new objects
--------------------

Object creation is usually handled by the `empty_data` action on your form. But if
you need more complex logic or want to supply default values, then you can override
the `newInstance` method. The request is passed as the only argument, and the return
value is used as the bound object on your creation form.

Example usage:

```php
protected function newInstance(Request $request)
{
    $type = $request->query->get('type');
    $class = $this->getConfiguration()->getEntityClass();

    return new $class($type);
}
```


Filtering the grid
------------------

To filter the items shown in the grid, override the `configureListCriteria` method. It is passed the
QueryBuilder as the only argument. Here you can add extra clauses. Example:

```php
protected function configureListCriteria(QueryBuilder $qb)
{
    $qb->andWhere($qb->getRootAlias() . '.deleted IS NULL');
}
```
