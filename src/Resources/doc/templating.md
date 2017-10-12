Templating
==========

The first thing you probably want to do is override the base template that is used for all 
actions. The base template is `@PrezentCrud/layout.html.twig`. To override it you can
create a template in `app/Resources/PrezentCrudBundle/views/layout.html.twig`. Be sure to include
the `crud_content` block where all content is rendered.

```twig
{# app/Resources/PrezentCrudBundle/views/layout.html.twig #}

{% extends 'base.html.twig' %}

{% block body %}
    <div class="crud-content">
        {% block crud_content %}{% endblock %}
    </div>
{% endblock %}
```

Template inheritance
--------------------

To customize the template of any action, just place the correct template in your bundle. The CrudController
automatically checks the entire inheritance chain of your controller and will pick the first template that
exists. For example, say you have the following controllers defined:

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

To customize the index template for your products controller, simply create the `AppBundle/Resources/views/Product/index.html.twig`
file. To customize the index view for all your admin controllers, create the `AppBundle/Resources/views/Admin/index.html.twig` file.
The CrudController will automatically use the first template that exists.


Flashes
-------

There is a generic template that you can include in your own custom actions to display all flashes.

```twig
{% include '@PrezentCrud/Common/flashes.html.twig' %}
```
