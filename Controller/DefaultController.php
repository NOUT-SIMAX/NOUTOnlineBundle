<?php

namespace NOUTBundleContextsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('NOUTContextesBundle:Default:index.html.twig', array('name' => $name));
    }
}
