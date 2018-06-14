<?php

namespace Prezent\CrudBundle\Tests\Functional;

use Prezent\CrudBundle\Tests\Fixture\Functional\AppBundle\Controller\ProductController;
use Prezent\CrudBundle\Tests\Fixture\Functional\AppFixtures;

/**
 * @author Sander Marechal
 */
class CrudControllerTest extends WebTestCase
{
    private $client;

    protected function setUp()
    {
        $this->client = self::createClient();
        self::createDatabase($this->client);
        ProductController::setConfigurator(null);
    }

    /**
     * @dataProvider pageProvider
     */
    public function testCrudPages($path)
    {
        self::loadFixtures($this->client, new AppFixtures(3));

        $crawler = $this->client->request('GET', $path);
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function pageProvider()
    {
        return [
            ['/product/'],
            ['/product/add'],
            ['/product/edit/1'],
        ];
    }

    public function testIndexPage()
    {
        self::loadFixtures($this->client, new AppFixtures(3));

        $crawler = $this->client->request('GET', '/product/');

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(1, $crawler->filter('table.crud-grid'));
        $this->assertCount(3, $crawler->filter('table.crud-grid thead th'));
        $this->assertCount(3, $crawler->filter('table.crud-grid tbody tr'));
        $this->assertCount(1, $crawler->filter('nav .current'));
    }

    public function testSort()
    {
        self::loadFixtures($this->client, new AppFixtures(3));

        $crawler = $this->client->request('GET', '/product/');
        $crawler = $this->client->click($crawler->selectLink('id')->link());

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(3, $crawler->filter('table.crud-grid tbody tr'));
        $this->assertCount(1, $crawler->filter('nav .current'));

        $this->assertEquals('3', trim($crawler->filter('tbody tr:nth-child(1) td')->first()->text()));
        $this->assertEquals('2', trim($crawler->filter('tbody tr:nth-child(2) td')->first()->text()));
        $this->assertEquals('1', trim($crawler->filter('tbody tr:nth-child(3) td')->first()->text()));
    }

    public function testAdd()
    {
        self::loadFixtures($this->client, new AppFixtures(3));

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/product/');
        $crawler = $this->client->click($crawler->selectLink('crud.product.index.action.add')->link());

        $form = $crawler->selectButton('form.product.submit')->form();
        $form['product_form[name]'] = 'quu';

        $crawler = $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(4, $crawler->filter('table.crud-grid tbody tr'));
        $this->assertCount(1, $crawler->filter('.callout:contains("flash.product.add.success")'));
    }

    public function testEdit()
    {
        self::loadFixtures($this->client, new AppFixtures(3));

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/product/');
        $crawler = $this->client->click($crawler->filter('a[href="/product/edit/1"]')->first()->link());

        $form = $crawler->selectButton('form.product.submit')->form();
        $this->assertEquals('product-1', $form['product_form[name]']->getValue());

        $form['product_form[name]'] = 'quu';
        $crawler = $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(3, $crawler->filter('table.crud-grid tbody tr'));
        $this->assertEquals('quu', trim($crawler->filter('tbody tr:nth-child(1) td:nth-child(2)')->text()));
        $this->assertCount(1, $crawler->filter('.callout:contains("flash.product.edit.success")'));
    }

    public function testDelete()
    {
        self::loadFixtures($this->client, new AppFixtures(3));

        $this->client->followRedirects();

        $crawler = $this->client->request('GET', '/product/');
        $crawler = $this->client->click($crawler->filter('a[href="/product/delete/1"]')->first()->link());

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(2, $crawler->filter('table.crud-grid tbody tr'));
        $this->assertCount(1, $crawler->filter('.callout:contains("flash.product.delete.success")'));
    }

    public function testPagination()
    {
        self::loadFixtures($this->client, new AppFixtures(15));

        $crawler = $this->client->request('GET', '/product/');

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(10, $crawler->filter('table.crud-grid tbody tr'));

        $crawler = $this->client->click($crawler->filter('a[rel="next"]')->link());

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertCount(5, $crawler->filter('table.crud-grid tbody tr'));
    }
}
