<?php

namespace OSU\CollageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('OSUCollageBundle:Default:index.html.twig', array('name' => $name));
    }
}
