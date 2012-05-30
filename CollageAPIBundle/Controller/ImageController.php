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

    //Pass $arr in as $_FILES['images']
    private function getImageArray($arr) {
        $result = [];
        foreach ($arr as $imageFile) {
            $fileName = $imageFile['tmp_name'];
            if ($imageFile['type']='image/jpeg') {
                $img = imagecreatefromjpeg($imageFile['tmp_name']);
            } else if ($imageFile['type'] = 'image/png') {
                $img = imagecreatefrompng($imageFile['tmp_name']);
            } else {
                $img = imagecreatefromgif($imageFile['tmp_name']);
            }
            $result[] = $img;
        }
        return $result;
    }

    private function resizeImage($img, $factor = 0.5) {
        $width = imagesx($img);
        $height = imagesy($img);
        $result = imagecreatetruecolor( $width /2 , $height/2);
        imagecopyresized($result, $img, 0, 0, 0, 0, $width * $factor, $height * $factor, $width, $height);

        return $result;
    }

}
