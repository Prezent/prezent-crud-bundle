Getting started
===============

A CRUD section consists of several components:

* A Doctrine managed object (entity or document)
* A form to create and edit objects
* A grid to list the objects

You should create these components yourself. Here are some example components that this documentation will use.

```php
// Doctrine entity

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM; 

/**
 * @ORM\Entity
 */
class Product
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $name;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    public $price;
}
```

```php
// Form type

namespace AppBundle\Form\Type;

use AppBundle\Entity\Product
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Type\TextType::class)
            ->add('price', Type\MoneyType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
```

```php
// Grid type

namespace AppBundle\Grid\Type;

use Prezent\Grid\BaseGridType;
use Prezent\Grid\Extension\Core\Type;
use Prezent\Grid\GridBuilder;

class ProductGridType extends BaseGridType
{
    public function buildGrid(GridBuilder $builder, array $options = [])
    {
        $builder
            ->addColumn('name', Type\StringType::class)
            ->addColumn('price', Type\StringType::class)
            ->addAction('edit', [
                'route' => 'app_product_edit',
                'route_parameters' => ['id' => '{id}'],
            ])
            ->addAction('delete', [
                'route' => 'app_product_delete',
                'route_parameters' => ['id' => '{id}'],
            ])
        ;
    }
}
```

With these components you can create a CRUD controller. The only requirement for such a 
controller is creating its configuration. Here is the minimal implementation of
the controller:

```php
namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\Type\ProductType;
use AppBundle\Grid\Type\ProductGridType;
use Prezent\CrudBundle\Controller\CrudController;
use Prezent\CrudBundle\Model\Configuration;

class ProductController extends CrudController
{
    protected function configure(Configuration $config)
    {
        $config
            ->setEntityClass(Product::class)
            ->setFormType(ProductType::class)
            ->setGridType(ProductGridType::class)
        ;
    }
}
```

Now you have a fully working product CRUD to list, add, edit and delete products.
