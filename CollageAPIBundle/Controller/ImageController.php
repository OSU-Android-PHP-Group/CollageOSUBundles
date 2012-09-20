<?php

namespace OSU\CollageAPIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
/*
 * See [FOSRestBundle Documentation](https://github.com/FriendsOfSymfony/FOSRestBundle/blob/master/Resources/doc/5-automatic-route-generation_single-restful-controller.md) 
 * for details.
 */

class ImageController extends Controller {

    public function getImageAction($slug) {
        return array(
            "name" => $slug,
            "success" => true
        );
    }

    public function postImageAction() {
        //return array("success" => true);
        ini_set("memory_limit","1024M"); 
        ob_start(); 
        //$imgArray = $this->getImageArray($_FILES['images']);
        //$width = imagesx($imgArray[0]);
        
        //$resultImg = $this->compositeSimple($_FILES['images']['tmp_name'], 10);
        $resultImg = $this->compositeImages($_FILES['images']['tmp_name']);
        //$resultImg = $this->testPop($_FILES['images']['tmp_name']);

        

        imagejpeg($resultImg);

        $content = ob_get_contents(); 
        ob_end_clean();
        return new Response($content, 200, array('content-type' => 'image/jpeg'));

    } 

    //Pass $arr in as $_FILES['images']
    private function getImageArray($arr) {
        $result = array();
        foreach ($arr['tmp_name'] as $tmp_name) {
            $img = imagecreatefromstring(file_get_contents($tmp_name));
            //$ getimagessizeresult[] = $img;
        }
        return $result;
    }
    private function compositeSimple($files, $padding = 0) {

        $len=count($files);
        $size = getimagesize($files[0]);
        $width = $size[0] / 2;
        $height = ($size[1] / 4) * $len + $padding * ($len - 1);

        $result = imagecreatetruecolor($width, $height);
        $starty = 0;

        foreach ($files as $name)
        {
            $image = imagecreatefromjpeg($name);
            $image = $this->resizeImage($image);
            $image = $this->cutHeight($image);
            imagecopy($result, $image, 0, $starty, 0, 0, imagesx($image), imagesy($image));
            $starty += imagesy($image) + $padding;
        }
        return $result;

    }

    private function testPop($arr)
    {
        $img = imagecreatefromjpeg(array_pop($arr));
        return $img;
    }

    //Return an image
    //$arr = array of tmp_name
    private function compositeImages($files) {
        $len = count($files);
        $size = getimagesize($files[0]);

        $width = $size[0];
       
        $height = ($size[1]) * (int) ($len / 3);
        if (($len % 3) != 0)
        {
            $height += $size[1]/2;
        }

        $result = imagecreatetruecolor($width, $height);
        $starty = 0;

        while ($len >= 3) 
        {
            $right_file = array_pop($files);
            $left_file = array_pop($files);
            $top_file = array_pop($files);
            $right = imagecreatefromjpeg($right_file);
            $left = imagecreatefromjpeg($left_file);
            $top = imagecreatefromjpeg($top_file);

            $top = $this->cutHeight($top);
            $right = $this->resizeImage($right);
            $left = $this->resizeImage($left);

            //Copy top part
            imagecopy($result, $top, 0, $starty, 0, 0, imagesx($top), imagesy($top));
            $starty += imagesy($top);

            //Copy left
            imagecopy($result, $left, 0, $starty, 0, 0, imagesx($left), imagesy($left));
            //Copy right
            imagecopy($result, $right, imagesx($left), $starty, 0, 0, imagesx($right), imagesy($right));
            
            $starty += imagesy($left);
            $len = count($files);
        }

        if ($len == 2)
        {
            $right = imagecreatefromjpeg(array_pop($files));
            $left = imagecreatefromjpeg(array_pop($files));

            $right = $this->resizeImage($right);
            $left = $this->resizeImage($left);

            //Copy left
            imagecopy($result, $left, 0, $starty, 0, 0, imagesx($left), imagesy($left));
            //Copy right
            imagecopy($result, $right, imagesx($left), $starty, 0, 0, imagesx($right), imagesy($right));
        } 

        if ($len == 1)
        {
            $top = imagecreatefromjpeg(array_pop($files));
            $top = $this->cutHeight($top);
            imagecopy($result, $top, 0, $starty, 0, 0, imagesx($top), imagesy($top));
        }

        return $result;
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
        $y = $height * ($totalAmount / 2);

        $result = imagecreatetruecolor( $width, $height - ($height * $totalAmount));
        imagecopy($result, $img, 0, 0, 0, $y, $width, $height - ($height * $totalAmount));

        return $result;
    }

}
