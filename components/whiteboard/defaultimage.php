<?php
header("Content-type: image/png");
$im = @imagecreate(800, 600)
    or die("Cannot Initialize new GD image stream");
$background_color = imagecolorallocate($im, 255, 255, 255);
imagepng($im);
imagedestroy($im);
?>