<?php

namespace OSU\CollageAPIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/*
 * See [FOSRestBundle Documentation](https://github.com/FriendsOfSymfony/FOSRestBundle/blob/master/Resources/doc/5-automatic-route-generation_single-restful-controller.md) 
 * for details.
 */

class ImageController extends Controller
{
    public function getImageAction($slug) {
        return array(
            "name" => $slug,
            "success" => true
        );
    }

    public function postImageAction() {
        return array("success" => true);
    }
}
