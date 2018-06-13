<?php

namespace Prezent\CrudBundle\Tests\Fixture\Functional\AppBundle\Grid;

use Prezent\Grid\BaseGridType;
use Prezent\Grid\Extension\Core\Type;
use Prezent\Grid\GridBuilder;

/**
 * @author Sander Marechal
 */
class ProductGrid extends BaseGridType
{
    /**
     * {@inheritDoc}
     */
    public function buildGrid(GridBuilder $builder, array $options = [])
    {
        $builder
            ->addColumn('id', Type\StringType::class, [
                'sortable' => true,
            ])
            ->addColumn('name', Type\StringType::class, [
                'sortable' => true,
            ])
            ->addAction('edit', [
                'route' => 'prezent_crud_tests_fixture_functional_app_product_edit',
                'route_parameters' => ['id' => '{id}'],
            ])
            ->addAction('delete', [
                'route' => 'prezent_crud_tests_fixture_functional_app_product_delete',
                'route_parameters' => ['id' => '{id}'],
            ])
        ;
    }
}
