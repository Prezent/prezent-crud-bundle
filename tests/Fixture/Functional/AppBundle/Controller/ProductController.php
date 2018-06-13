<?php

namespace Prezent\CrudBundle\Tests\Fixture\Functional\AppBundle\Controller;

use Prezent\CrudBundle\Controller\CrudController;
use Prezent\CrudBundle\Model\Configuration;
use Prezent\CrudBundle\Tests\Fixture\Functional\AppBundle\Entity\Product;
use Prezent\CrudBundle\Tests\Fixture\Functional\AppBundle\Form\ProductForm;
use Prezent\CrudBundle\Tests\Fixture\Functional\AppBundle\Grid\ProductGrid;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Sander Marechal
 */
class ProductController extends CrudController
{
    /**
     * {@inheritDoc}
     */
    protected function configure(Request $request, Configuration $config)
    {
        $config
            ->setEntityClass(Product::class)
            ->setFormType(ProductForm::class)
            ->setGridType(ProductGrid::class)
        ;
    }
}
