prezent/crud-bundle
===================

[![Build Status](https://travis-ci.org/Prezent/prezent-crud-bundle.svg?branch=master)](https://travis-ci.org/Prezent/prezent-crud-bundle)

Easy CRUD for Symfony

```php
namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\Type\ProductType;
use AppBundle\Grid\Type\ProductGridType;
use Prezent\CrudBundle\Controller\CrudController;
use Prezent\CrudBundle\Model\Configuration;

class ProductController extends CrudController
{
    protected function configure(Request $request, Configuration $config)
    {
        $config
            ->setEntityClass(Product::class)
            ->setFormType(ProductType::class)
            ->setGridType(ProductGridType::class)
        ;
    }
}
```

The documentation can be found in [Resources/doc](src/Resources/doc/index.md)
