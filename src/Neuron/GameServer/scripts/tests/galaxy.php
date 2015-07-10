<?php

$width = 3000;
$height = 3000;

$x = floor ($width / 2);
$y = floor ($height / 2);

$offsetX = $width / 2;
$offsetY = $height / 2;

$origin = new Neuron_GameServer_Map_Location ($x, $y);
$area = new Neuron_GameServer_Map_Area ($origin, max ($width, $height) * Andromeda_Universe_Universe::STAR_RESOLUTION);

$im = imagecreate ($width, $height);

$stars = Andromeda_Universe_Universe::getStarSystems ($area);

$black = imagecolorallocate($im, 0, 0, 0);
$white = imagecolorallocate($im, 255, 255, 255);
foreach ($stars as $v)
{
	$l = $v->getLocation ();
	$l = $l->scale (1 / Andromeda_Universe_Universe::STAR_RESOLUTION);
	imagesetpixel ($im, $l[0] + $offsetX, $l[1] + $offsetY, $white);
}

imagestring ($im, 1, 5, 5, count ($stars) . ' stars', $white);

header("content-type:image/png");
imagepng ($im);