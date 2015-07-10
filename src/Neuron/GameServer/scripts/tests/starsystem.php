<?php

$zoom = 10;

$width = 3000;
$height = 3000;

$x = 0;
$y = 0;

$offsetX = $width / 2;
$offsetY = $height / 2;

$origin = new Neuron_GameServer_Map_Location ($x, $y);
$area = new Neuron_GameServer_Map_Area ($origin, max ($width, $height) * $zoom);

global $im;
$im = imagecreate ($width, $height);

$stars = Andromeda_Universe_Universe::getBodies ($area);

$black = imagecolorallocate($im, 0, 0, 0);
$white = imagecolorallocate($im, 255, 255, 255);

global $colors;
$colors = array ();
function cache_color ($color)
{
	global $im;
	global $colors;

	if (!isset ($colors[$color->getHex ()]))
	{
		$colors[$color->getHex ()] = imagecolorallocate($im, $color->r (), $color->g (), $color->b ());
	}
	return $colors[$color->getHex ()];
}

foreach ($stars as $v)
{
	$l = $v->getLocation ();
	$l = $l->scale (1 / $zoom);
	$color = cache_color ($v->getDisplayObject ()->getColor ());

	imagerectangle ($im, $l[0] + $offsetX, $l[1] + $offsetY, $l[0] + $offsetX + 10, $l[1] + $offsetY + 10, $color);
}

imagestring ($im, 1, 5, 5, count ($stars) . ' bodies', $white);

$stars = Andromeda_Universe_Universe::getStarSystems ($area);
imagestring ($im, 1, 5, 15, count ($stars) . ' stars', $white);

header("content-type:image/png");
imagepng ($im);