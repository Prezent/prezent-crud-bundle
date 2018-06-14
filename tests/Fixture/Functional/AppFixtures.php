<?php

namespace Prezent\CrudBundle\Tests\Fixture\Functional;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Prezent\CrudBundle\Tests\Fixture\Functional\AppBundle\Entity\Product;

/**
 * @author Sander Marechal
 */
class AppFixtures extends AbstractFixture
{
    /**
     * @param int $num
     */
    public function __construct($num)
    {
        $this->num = $num;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $om)
    {
        for ($i = 1; $i <= $this->num; $i++) {
            $product = new Product();
            $product->setName('product-' . $i);

            $om->persist($product);
        }

        $om->flush();
    }
}
