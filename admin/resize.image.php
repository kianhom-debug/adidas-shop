<?php

function resize_image($file, $target) {
    list($w, $h) = getimagesize($file);
    $new_w = 300;
    $new_h = 300;

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if ($ext == 'png') {
        $src = imagecreatefrompng($file);
    } elseif ($ext == 'jpg' || $ext == 'jpeg') {
        $src = imagecreatefromjpeg($file);
    } else {
        return;
    }
    
    $dst = imagecreatetruecolor($new_w, $new_h);
    
    imagealphablending($dst, false);
    imagesavealpha($dst, true);

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_w, $new_h, $w, $h);
    
    if ($ext == 'png') {
        imagepng($dst, $target);
    } else {
        imagejpeg($dst, $target, 90);
    }
    
    imagedestroy($src);
    imagedestroy($dst);
}
?>