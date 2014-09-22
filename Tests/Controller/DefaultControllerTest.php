<?php

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'app.php/online/test');

	    //echo $crawler->html();

	    $nNb = $crawler->filter('html:contains("NOUTOnline")')->count();
	    echo "\n\nNb NOUTOnline = ".$nNb;

        $this->assertTrue($nNb > 0);

    }
}
