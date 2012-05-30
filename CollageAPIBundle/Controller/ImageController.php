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
        //return array("success" => true);
    }

    //Pass $arr in as $_FILES['images']
    private function getImageArray($arr) {
        $result = [];
        foreach ($arr['tmp_name'] as $tmp_name) {
            $img = imagecreatefromstring(file_get_contents($tmp_name));
            $result[] = $img;
        }
        return $result;
    }

    //Return an image
    private function compositeImages($arr, $padding = 0) {
        $len = count($arr);
        if ($len > 3) {
            $remainder = $len % 3;
            $rest = [];
            if ($remainder == 0) {
                for ($i = 0; $i < 3; $i++) {
                    $rest[] = array_pop($arr);
                }
            } else {
                for ($i = 0; $i < $len; $i++) {
                    $rest[] = array_pop($arr);
                }
            }

            $first = compositeImages($arr, $padding);
            $second = compositeImages($rest, $padding);

            $resultImg = imagecreatetruecolor(imagesx($first), imagesy($first) + imagesy($second) + $padding);
            imagecopy($resultImg, $first, 0, 0, 0, 0, imagesx($first), imagesy($first));
            imagecopy($resultImg, $second, 0, imagesy($first) + $padding, imagesx($second), imagesy($second));

        } else if ($len == 3) {
            $rest[] = array_pop($arr);
            $rest[] = array_pop($arr);

            $first = compositeImages($arr, $padding);
            $second = compositeImages($rest, $padding);

            $resultImg = imagecreatetruecolor(imagesx($first), imagesy($first) + imagesy($second) + $padding);
            imagecopy($resultImg, $first, 0, 0, 0, 0, imagesx($first), imagesy($first));
            imagecopy($resultImg, $second, 0, imagesy($first) + $padding, imagesx($second), imagesy($second));

        } else if ($len == 2) {
            $left = array_pop($arr);
            $right = array_pop($arr);

            $resultImg = imagecreatetruecolor(imagesx($left), imagesy($left));

            $left = resizeImage($left);
            $right = resizeImage($right);

            imagecopy($resultImg, $left,0, 0, 0, 0, imagesx($left), imagesy($left));
            imagecopy($resultImg, $right, imagesx($left),0, 0, 0, imagesx($right), imagesy($right));

        } else if {$len == 1) {
            $first = array_pop($arr);
            $resultImg = cutHeight($img);
        } else {
            $resultImg = imagecreatetruecolor(0, 0);
        }
    }

    private function resizeImage($img, $factor = 0.5) {
        $width = imagesx($img);
        $height = imagesy($img);
        $result = imagecreatetruecolor( $width * $factor , $height * $factor);
        imagecopyresized($result, $img, 0, 0, 0, 0, $width * $factor, $height * $factor, $width, $height);

        return $result;
    }

    // Cut the top and bottom of the image
    private function cutHeight($img, $totalAmount = 0.5) {

        $width = imagesx($img);
        $height = imagesy($img);
        $y = $height * ($amount / 2);

        $result = imagecreatetruecolor( $width, $height - ($height * $amount));
        imagecopy($result, $img, 0, 0, 0, $y, $width, $height - ($height * $amount));

        return $result;
    }

}
