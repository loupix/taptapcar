<?php

namespace AnnonceBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnnonceControllerTest extends WebTestCase
{
    public function testTaxifind()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/taxiFind');
    }

    public function testVtcfind()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/vtcFind');
    }

    public function testCovoituragefind()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/covoiturageFind');
    }

    public function testDemenagementfind()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/demenagementFind');
    }

    public function testGet()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/get');
    }

}
