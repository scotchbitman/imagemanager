<?php
	require 'lib/class/Manager.class.php';
	require 'lib/class/Canvas.class.php';
	
	$man = new Manager(
		array(
			'maxWidth' => 420,
		)
	);
	$img = new Canvas('img/portrait.jpg');
	
	$man->makeThumb($img);
?>
