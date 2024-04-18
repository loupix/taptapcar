<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class appControllerTest extends WebTestCase
{
    public function testCcm()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/ccm');
    }

    public function testAssurance()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/assurance');
    }

    public function testAnnulations()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/annulations');
    }

    public function testFaq()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/faq');
    }

    public function testInfos()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/infos');
    }

    public function testCookies()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/cookies');
    }

    public function testCgu()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/cgu');
    }

    public function testCharte()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/charte');
    }

}
