<?php
Header("Content-type: image/png");
$im = imagecreatetruecolor(55, 30);
$white = imagecolorallocate($im, 0, 128, 0);

// Draw a white rectangle
imagefilledrectangle($im, 4, 4, 50, 25, $white);
imagepng($im);
imagedestroy($im);
?>