<?php defined('SYSPATH') or die('No direct script access.');

abstract class Image extends Kohana_Image {
    
    public static function blob_resize($blob, $w, $h, $q=100) {
        $im = imagecreatefromstring($blob);

        $new = imagecreatetruecolor($w, $h);

        $x = imagesx($im);

        $y = imagesy($im);

        imagecopyresampled($new, $im, 0, 0, 0, 0, $w, $h, $x, $y);

        imagedestroy($im);

        ob_start();
        imagejpeg($new, null, $q);
        $new_blob = ob_get_contents();
        ob_end_clean();

        return $new_blob;
    }
}

