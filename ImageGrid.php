<?php
/**
 * ImageGrid.php
 * Class for image grid creation
 *
 * @author Daniel Ribes <daniel@danielribes.com>
 */



class ImageGrid {

	private $imgOnGridWidth;
	private $imgOnGridHeight;
	public $input_path;
	public $output_path;
	public $totalimages; // count images processed


	public function __construct()
	{
		// Set defaults
		$this->input_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'images';
		$this->output_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'output';
		$this->totalimages = 0;
	}


	/**
	 * Set the images source path
	 * @param String $ipath Images source path
	 */
	public function setInputPath( $thepath )
	{
		$this->input_path = $thepath;
	}


	/**
	 * Set the images output path
	 * @param String $ipath Images output path
	 */
	public function setOutputPath( $thepath )
	{
		$this->output_path = $thepath;
	}


	/**
	 * Make a very basic image grid.
	 * Create a image file with basic grid of images loads from directory
	 *
	 * @param  Integer $cols      Number of grid columns
	 * @param  Integer $gwidth    Grid image width in pixels
	 * @param  String $gridname   Name of final grid image file
	 * @return [type]             TRUE on succes otherwise FALSE
	 */
	public function basicGrid($cols, $gwidth, $gridname)
	{

		$images_group = $this->getFileImages($this->input_path);

		$imgRef = $this->input_path.DIRECTORY_SEPARATOR.$images_group[0];
		$imgCanvas = $this->createBaseCanvasImage(count($images_group), $cols, $gwidth, $imgRef);

		// Process every image to add on canvas
		$xCanvas = 0;
		$yCanvas = 0;

		$aCol = 1;
		$aRow = 1;

		foreach ($images_group as $oneImage)
		{
			$imgFile = $this->input_path.DIRECTORY_SEPARATOR.$oneImage;
			list($imgWidth, $imgHeight) = getimagesize($imgFile);
			$imgOnGridHeight = round( (($imgHeight * $this->getImgOnGridWidth()) / $imgWidth) , 0);

			$img = imagecreatefromjpeg($imgFile);
			imagecopyresampled($imgCanvas, $img, $xCanvas, $yCanvas, 0, 0, $this->getImgOnGridWidth(),
				                                                           $imgOnGridHeight,
				                                                           $imgWidth,
				                                                           $imgHeight);
			$this->totalimages++;

			if( $aCol < $cols )
			{
				$xCanvas += $this->getImgOnGridWidth();
				$aCol++;
			} else {
				$xCanvas = 0;
				$aCol = 1;
				$yCanvas += $imgOnGridHeight;
			}

		}

		// Save the final canvas image
		$ih = imagejpeg($imgCanvas, $this->output_path.DIRECTORY_SEPARATOR.$gridname.'.jpg', 100);
		if($ih)
		{
			return TRUE;
		} else {
			return FALSE;
		}
	}




	/**
	 * Getter for the Witdh of one image inside the grid
	 * @return Integer Width of one image on grid
	 */
	public function getImgOnGridWidth()
	{
		return $this->imgOnGridWidth;
	}


	/**
	 * Getter for the Height of one image inside the grid
	 * @return Integer Height of one image on grid
	 */
	public function getImgOnGridHeight()
	{
		return $this->imgOnGridHeight;
	}


//=============================================================================
// Private methods
//=============================================================================

	/**
	 * Create the base canvas image for the grid
	 * @param  Integer $imageFileCount Total images to process
	 * @param  Integer $cols           Total cols of grid
	 * @param  Integer $imgCanvasWidth Width of the image grid
	 * @param  ImgObj $imgRef         First image oon the images set
	 * @return ImgObj                 Canvas image grid created
	 *
	 */
	private function createBaseCanvasImage($imageFileCount, $cols, $imgCanvasWidth, $imgRef)
	{
		// Define individual image width
		$this->imgOnGridWidth = round(($imgCanvasWidth / $cols), 0);

		// Define and create the output canvas image
		list($imgWidth, $imgHeight) = getimagesize($imgRef);
		$this->imgOnGridHeight = round( (($imgHeight * $this->imgOnGridWidth) / $imgWidth) , 0);
		$rows = ceil($imageFileCount/$cols);
		$imgCanvasHeight = $this->imgOnGridHeight * $rows;
		$imgCanvas = imagecreatetruecolor($imgCanvasWidth, $imgCanvasHeight);

		$backgroundcolor = imagecolorallocate($imgCanvas, 255, 255, 255);
		imagefill($imgCanvas, 0, 0, $backgroundcolor);

		return $imgCanvas;
	}


	/**
	 *  Get files from directory. Remove ., .. and .DS_Store
	 *  @param String $path Path of files
	 *  @return Array Image files
	 *
	 *
	 */
	private function getFileImages($path)
	{
		$images = array_diff(scandir($path), array('..', '.', '.DS_Store'));
		return array_values($images);
	}

}
