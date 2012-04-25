<?php

namespace OSU\CollageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class ImageController extends Controller
{
    public function getImageAction() {
        return array("success" => true);
    }
}
