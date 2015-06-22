<?php

namespace NOUT\Bundle\ContextsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('NOUTContextsBundle:Default:index.html.twig', array('name' => $name));
    }
}
