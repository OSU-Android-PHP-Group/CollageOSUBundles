<?php

namespace OSU\CollageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction() {
        return $this->render('OSUCollageBundle:Default:index.html.twig');
    }
}
