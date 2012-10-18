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

    public function getTestAction() {
        $proto = !empty($_SERVER['HTTPS']) ? "https" : "http";
        $host = $proto . '://' . $_SERVER['HTTP_HOST'];
        $content = $this->renderView('OSUCollageAPIBundle:Image:post.html.twig', array('host' => $host));
        return new Response($content, 200, array('content-type' => 'text/html'));
    }

    public function postImageAction() {
        //return array("success" => true);
        //$imgArray = $this->getImageArray($_FILES['images']);
        //$width = imagesx($imgArray[0]);


        //$resultImg = $this->compositeSimple($_FILES['images']['tmp_name'], 10);
        //$resultImg = $this->compositeImages($_FILES['images']['tmp_name']);
        //$resultImg = $this->testPop($_FILES['images']['tmp_name']);
        try {
            if (!(array_key_exists('images', $_FILES)) || (in_array( "", $_FILES['images']['tmp_name'])))
            {
                throw new \Exception ("There was some problems uploading images.");
            }

            // build $image_array
            $image_array = array();
            for ($i = 0; $i < count($_FILES['images']['tmp_name']); $i++) {
                $image_array[$i] = array(
                    'tmp_name' => $_FILES['images']['tmp_name'][$i],
                    'name' => $_FILES['images']['name'][$i]
                    );
            }

            $resultImg = $this->compositeWithTree($image_array);
        } catch (\Exception $e){
            return new Response($e->getMessage(), 400, array('content-type' => 'text/html'));
        }

        ob_start();
        imagejpeg($resultImg);

        $content = ob_get_contents(); 
        ob_end_clean();
        return new Response($content, 200, array('content-type' => 'image/jpeg'));

    } 

    private function Build_Layout_Tree($w, $h, $num_of_el) {
        $result = array();
        if ($num_of_el > 1) {
            $node_type = "box";
            // set up root
            $result["width"] = $w;
            $result["height"] = $h;
            $result["node_type"] = $node_type;

            //calculate children's width and height
            $proportion = mt_rand(40, 60)/100;
            //$proportion = 0.5;
            if ($w > $h) { // current box is horizontal, divide along width
                $l_width = floor($w * $proportion);
                $r_width = $w - $l_width;
                $l_height = $h;
                $r_height = $h;
            } else { // vertical box, divide along height
                $l_height = floor($h * $proportion);
                $r_height = $h - $l_height;
                $l_width = $w;
                $r_width = $w;
            }

            //number of element on each side
            $l_size = ceil($num_of_el / 2);
            $r_size = $num_of_el - $l_size;

            //build children
            $result["left"] = $this->Build_Layout_Tree($l_width, $l_height, $l_size);
            $result["right"] = $this->Build_Layout_Tree($r_width, $r_height, $r_size);

        } else { // leaf node
            $node_type = "img";
            $result["width"] = $w;
            $result["height"] = $h;
            $result["node_type"] = $node_type;
            $result["left"] = array();
            $result["right"] = array();
        }
        return $result;
    }

    /*
     * Get image, then crop and resize by first scaling the image up or down and cropping a specified area from the center.
     * string $file_name: path to image
     * int $w, $h:  target width and height
     *
     * return an image
     */
    private function getImage($file_name, $target_width, $target_height) {

        try {
            $img = imagecreatefromstring(file_get_contents($file_name));
        } catch (\Exception $e) {
            return false;
        }

        list($width, $height) = getimagesize($file_name);
        $result = imagecreatetruecolor($target_width, $target_height);

        $scale = max($target_width/$width, $target_height/$height);
        imagecopyresampled($result, $img, 0, 0, ($width - $target_width / $scale) / 2, ($height - $target_height / $scale) / 4, $target_width, $target_height, $target_width / $scale, $target_height / $scale);

        return $result;
    }

    // image_array is an array of [tmp_name, name]
    private function Draw_Collage($tree, &$image_array, &$canvas, $startx, $starty, $border = 3)
    {
        if ($tree["node_type"] == "box") {
            // Horizontal
            if ($tree["width"] > $tree["height"]) {
                $this->Draw_Collage($tree["left"], $image_array, $canvas, $startx, $starty);
                $this->Draw_Collage($tree["right"], $image_array, $canvas, $startx + $tree["left"]["width"], $starty);
            } else {
                // Vertical
                $this->Draw_Collage($tree["left"], $image_array, $canvas, $startx, $starty);
                $this->Draw_Collage($tree["right"], $image_array, $canvas, $startx, $starty + $tree["left"]["height"]);
            }
        } else { // image
            $pair= array_pop($image_array);
            $path = $pair['tmp_name'];
            $name = $pair['name'];
            $current_img = $this->getImage($path, $tree["width"], $tree["height"]);
            if ($current_img == false) {
                throw new \Exception("Image \"" . $name . "\" cannot be loaded.");
            }
            $white = imagecolorallocate($current_img, 255, 255, 255);
            $gray = imagecolorallocate($current_img, 150, 150, 150);

            //white border
            imagesetthickness($current_img, $border * 2);
            imageline($current_img, 0, 0, imagesx($current_img), 0,$white); // top border
            imageline($current_img, 0, 0, 0, imagesy($current_img),$white); // left border

            imageline($current_img, 0, imagesy($current_img), imagesx($current_img), imagesy($current_img),$white); // bottom border
            imageline($current_img, imagesx($current_img), 0, imagesx($current_img), imagesy($current_img),$white); // right border

            //grey border
            imagesetthickness($current_img, 1);
            imagerectangle($current_img, $border, $border, imagesx($current_img) - $border, imagesy($current_img) - $border, $gray);

            imagecopy($canvas, $current_img, $startx, $starty, 0, 0, imagesx($current_img), imagesy($current_img));

            //border
        }
    }

    private function compositeWithTree($file_array, $w = 1600, $h = 900)
    {
        shuffle($file_array);
        $tree = $this->Build_Layout_Tree($w, $h, count($file_array));
        $image = imagecreatetruecolor($w, $h);
        $this->Draw_Collage($tree, $file_array, $image, 0, 0);

        return $image;
    }

    // OLD function ======================

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
