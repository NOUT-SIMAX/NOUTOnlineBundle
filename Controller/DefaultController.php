<?php

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('NOUTNOUTOnlineBundle:Default:index.html.twig', array('name' => $name));
    }
}
