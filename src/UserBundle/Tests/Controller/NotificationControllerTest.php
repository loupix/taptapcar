<?php

namespace UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationControllerTest extends WebTestCase
{
    public function testGet()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/get');
    }

    public function testCount()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/count');
    }

    public function testDel()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/del');
    }

    public function testRead()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/read');
    }

}
