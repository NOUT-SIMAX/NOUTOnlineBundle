<?php

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('NOUTOnlineBundle:Default:index.html.twig');
    }
}
