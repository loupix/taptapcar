<?php

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationControllerTest extends WebTestCase
{
    public function testAdd()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/add');
    }

    public function testDel()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/del');
    }

    public function testModif()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/modif');
    }

    public function testVue()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/vue');
    }

    public function testGetall()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/getAll');
    }

}
