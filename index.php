<?php
	require 'lib/class/ImageManager.class.php';
	require 'lib/class/Canvas.class.php';
	
	$man = new ImageManager(
		array(
			'maxWidth' => 420,
		)
	);
	$img = new Canvas('img/portrait.jpg');
	
	$man->makeThumb($img);
?>
