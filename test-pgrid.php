<?php

// ------------------------------------------------
//    Test pgrid.php
// ------------------------------------------------

$filename = "gridimage_test";

require_once('ImageGrid.php');

$grid = new ImageGrid();

if ( extension_loaded('gd') )
{
	$grid->setInputPath(dirname(__FILE__).DIRECTORY_SEPARATOR.'images2');
	$grid->setOutputPath(dirname(__FILE__).DIRECTORY_SEPARATOR.'output');
	if( $grid->basicGrid(2,750, $filename) )
	{
		echo "Done!\n";
		echo $grid->totalimages." images processed.\n";
	} else {
		echo "Fail!, I can't create the image grid\n";
	}
} else {
	echo "ImageGrid need GD Extension to run";
}
