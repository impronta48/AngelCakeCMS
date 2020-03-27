<?php
/**
 * Image Helper class file.
 *
 * Generate an image with a specific size.
 *
 *
 * @package       Cake.View.Helper
 * @since         CakePHP(tm) v 3
 */

namespace App\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;
use Cake\Filesystem\Folder;

/**
 * Image Helper class for generate an image with a specific size.
 *
 * ImageHelper encloses 2 method needed while resizing images.
 *
 * @package       View.Helper
 */
class ImageHelper extends Helper
{

    public $helpers = ['Html'];

    /**
     * Generate an image with a specific size
     * @param  string   $image   Path of the image (from the webroot directory)
     * @param  int      $width
     * @param  int      $height
     * @param  array    $options Options (same that HtmlHelper::image)
     * @param  int      $quality
     * @return string   <img> tag
     */
    public function resize($image, $width, $height, $options = array(), $quality = 100)
    {
        $options['width'] = $width;
        $options['height'] = $height;
        return $this->Html->image($this->resizedUrl($image, $width, $height, $quality), $options);
    }


    /**
     * Create an image with a specific size
     * @param  string   $file   Path of the image (from the webroot directory)
     * @param  int      $width
     * @param  int      $height
     * @param  array    $options Options (same that HtmlHelper::image)
     * @param  int      $quality
     * @return string   image path
     */
    public function resizedUrl($file, $width, $height, $quality = 100)
    {

        # We define the image dir include Theme support
        //Massimoi: spengo per facilità la gestione dei temi
        //$imageDir = (!isset($this->theme)) ? IMAGES : APP.'View'.DS.'Themed'.DS.$this->theme.DS.'webroot'.DS.'img'.DS;        
        //Se l'immagine inizia con / immagino che l'url sia relativo a WWW_ROOT altrimenti a IMAGES   
        if (empty($file)) {
            return '';
        }
        if ($file[0] == DS) {
            $imageDir = WWW_ROOT;   //Se lascio così assumo che tutte le immagini siano in img
            $startChar = DS;
        } else {
            $imageDir = WWW_ROOT .  'img' . DS;   //Se lascio così assumo che tutte le immagini possano essere in www root
            $startChar = '';
        }
        $maxmem = Configure::read('MaxMemoryImageResize');
        if (empty($maxmem)) {
            $maxmem = '256M';
        }
        ini_set('memory_limit', $maxmem);

        # We find the right file
        $pathinfo   = pathinfo(trim($file, '/'));
        $file       = $imageDir . trim($file, '/');        
        //Massimoi - 25/6/18 - modifico output per gestire cartelle invece di _ dimensione
        //$output     = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '_' . $width . 'x' . $height . '.' . $pathinfo['extension'];
        $folder = new Folder();
        $folder->create($imageDir . DS . $pathinfo['dirname'] . DS . $width . 'x' . $height, 0777, true);
        $output     = $startChar . $pathinfo['dirname'] . DS . $width . 'x' . $height . DS . $pathinfo['filename'] . '.' . $pathinfo['extension'];
                
        if (!file_exists($imageDir . $output)) {

            # Setting defaults and meta
            $info                         = getimagesize($file);
            list($width_old, $height_old) = $info;

            # Create image ressource
            switch ($info[2]) {
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($file);
                    break;
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($file);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($file);
                    break;
                default:
                    return false;
            }

            # We find the right ratio to resize the image before cropping
            $heightRatio = $height_old / $height;
            $widthRatio  = $width_old /  $width;

            $optimalRatio = $widthRatio;
            if ($heightRatio < $widthRatio) {
                $optimalRatio = $heightRatio;
            }
            $height_crop = ($height_old / $optimalRatio);
            $width_crop  = ($width_old  / $optimalRatio);

            # The two image ressources needed (image resized with the good aspect ratio, and the one with the exact good dimensions)
            $image_crop = imagecreatetruecolor($width_crop, $height_crop);
            $image_resized = imagecreatetruecolor($width, $height);

            # This is the resizing/resampling/transparency-preserving magic
            if (($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG)) {
                $transparency = imagecolortransparent($image);
                if ($transparency >= 0) {
                    $transparent_color  = imagecolorsforindex($image, $trnprt_indx);
                    $transparency       = imagecolorallocate($image_crop, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                    imagefill($image_crop, 0, 0, $transparency);
                    imagecolortransparent($image_crop, $transparency);
                    imagefill($image_resized, 0, 0, $transparency);
                    imagecolortransparent($image_resized, $transparency);
                } elseif ($info[2] == IMAGETYPE_PNG) {
                    imagealphablending($image_crop, false);
                    imagealphablending($image_resized, false);
                    $color = imagecolorallocatealpha($image_crop, 0, 0, 0, 127);
                    imagefill($image_crop, 0, 0, $color);
                    imagesavealpha($image_crop, true);
                    imagefill($image_resized, 0, 0, $color);
                    imagesavealpha($image_resized, true);
                }
            }

            imagecopyresampled($image_crop, $image, 0, 0, 0, 0, $width_crop, $height_crop, $width_old, $height_old);
            imagecopyresampled($image_resized, $image_crop, 0, 0, ($width_crop - $width) / 2, ($height_crop - $height) / 2, $width, $height, $width, $height);


            # Writing image according to type to the output destination and image quality
            switch ($info[2]) {
                case IMAGETYPE_GIF:
                    imagegif($image_resized, $imageDir . $output, $quality);
                    break;
                case IMAGETYPE_JPEG:
                    imagejpeg($image_resized, $imageDir . $output, $quality);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($image_resized, $imageDir . $output, 9);
                    break;
                default:
                    return false;
            }
        }

        return $output;
    }
}
