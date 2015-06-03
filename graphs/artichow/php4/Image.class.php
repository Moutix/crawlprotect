<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */
 


/* <php4> */

define("IMAGE_JPEG", 1);
define("IMAGE_PNG", 2);
define("IMAGE_GIF", 3);

/* </php4> */

/*
 * Register a class with the prefix in configuration file
 */
function registerClass($class, $abstract = FALSE) {

	if(ARTICHOW_PREFIX === 'aw') {
		return;
	}
	
	
	$abstract = '';
	

	eval($abstract." class ".ARTICHOW_PREFIX.$class." extends aw".$class." { }");

}

/*
 * Register an interface with the prefix in configuration file
 */
function registerInterface($interface) {

	if(ARTICHOW_PREFIX === 'aw') {
		return;
	}


}

// Some useful files
require_once ARTICHOW."/Component.class.php";

require_once ARTICHOW."/inc/Grid.class.php";
require_once ARTICHOW."/inc/Tools.class.php";
require_once ARTICHOW."/inc/Drawer.class.php";
require_once ARTICHOW."/inc/Math.class.php";
require_once ARTICHOW."/inc/Tick.class.php";
require_once ARTICHOW."/inc/Axis.class.php";
require_once ARTICHOW."/inc/Legend.class.php";
require_once ARTICHOW."/inc/Mark.class.php";
require_once ARTICHOW."/inc/Label.class.php";
require_once ARTICHOW."/inc/Text.class.php";
require_once ARTICHOW."/inc/Color.class.php";
require_once ARTICHOW."/inc/Font.class.php";
require_once ARTICHOW."/inc/Gradient.class.php";
require_once ARTICHOW."/inc/Shadow.class.php";
require_once ARTICHOW."/inc/Border.class.php";
 
require_once ARTICHOW."/common.php";
 
/**
 * An image for a graph
 *
 * @package Artichow
 */
class awImage {

	/**
	 * Graph width
	 *
	 * @var int
	 */
	var $width;

	/**
	 * Graph height
	 *
	 * @var int
	 */
	var $height;
	
	/**
	 * Use anti-aliasing ?
	 *
	 * @var bool
	 */
	var $antiAliasing = FALSE;
	
	/**
	 * Image format
	 *
	 * @var int
	 */
	var $format = IMAGE_PNG;
	
	/**
	 * Image background color
	 *
	 * @var Color
	 */
	var $background;
	
	/**
	 * GD resource
	 *
	 * @var resource
	 */
	var $resource;
	
	/**
	 * Image drawer
	 *
	 * @var Drawer
	 */
	var $drawer;
	
	/**
	 * Shadow
	 *
	 * @var Shadow
	 */
	var $shadow;
	
	/**
	 * Image border
	 *
	 * @var Border
	 */
	var $border;
	
	/**
	 * Use JPEG for image
	 *
	 * @var int
	 */
	
	
	/**
	 * Use PNG for image
	 *
	 * @var int
	 */
	
	
	/**
	 * Use GIF for image
	 *
	 * @var int
	 */
	
	
	/**
	 * Build the image
	 */
	 function awImage() {
		
		$this->background = new awColor(255, 255, 255);
		$this->shadow = new awShadow(SHADOW_RIGHT_BOTTOM);
		$this->border = new awBorder;
		
	}
	
	/**
	 * Get drawer of the image
	 *
	 * @param int $w Drawer width (from 0 to 1) (default to 1)
	 * @param int $h Drawer height (from 0 to 1) (default to 1)
	 * @param float $x Position on X axis of the center of the drawer (default to 0.5)
	 * @param float $y Position on Y axis of the center of the drawer (default to 0.5)
	 * @return Drawer
	 */
	 function getDrawer($w = 1, $h = 1, $x = 0.5, $y = 0.5) {
		$this->create();
		$this->drawer->setSize($w, $h);
		$this->drawer->setPosition($x, $y);
		return $this->drawer;
	}
	
	/**
	 * Change the image size
	 *
	 * @var int $width Image width
	 * @var int $height Image height
	 */
	 function setSize($width, $height) {
	
		if($width !== NULL) {
			$this->width = (int)$width;
		}
		if($height !== NULL) {
			$this->height = (int)$height;
		}
	
	}
	
	/**
	 * Change image background color
	 *
	 * @param $color
	 */
	 function setBackgroundColor($color) {
		$this->background = $color;
	}
	
	/**
	 * Change image background gradient
	 *
	 * @param $gradient
	 */
	 function setBackgroundGradient($gradient) {
		$this->background = $gradient;
	}
	
	/**
	 * Can we use anti-aliasing ?
	 *
	 * @var bool $bool
	 */
	 function setAntiAliasing($bool) {
		$this->antiAliasing = (bool)$bool;
	}
	
	/**
	 * Change image format
	 *
	 * @var int $format New image format
	 */
	 function setFormat($format) {
		if($format === IMAGE_JPEG or $format === IMAGE_PNG or $format === IMAGE_GIF) {
			$this->format = $format;
		}
	}
	
	/**
	 * Create a new awimage
	 */
	 function create() {
	
		if($this->resource === NULL) {
	
			// Create image
			
			$this->resource = imagecreatetruecolor($this->width, $this->height);
			
			if(!$this->resource) {
				awImage::drawError("Class Image: Unable to create a graph.");
			}
			
			imagealphablending($this->resource, TRUE);
			
			if($this->antiAliasing) {
				if(function_exists('imageantialias')) {
					imageantialias($this->resource, TRUE);
				} else {
					awImage::drawErrorFile('missing-anti-aliasing');
				}
			}
			
			$this->drawer = new awDrawer($this->resource);
			$this->drawer->setImageSize($this->width, $this->height);
			
			// Original color
			$this->drawer->filledRectangle(
				new awWhite,
				new awLine(
					new awPoint(0, 0),
					new awPoint($this->width, $this->height)
				)
			);
		
			$shadow = $this->shadow->getSpace();
			
			$p1 = new awPoint($shadow->left, $shadow->top);
			$p2 = new awPoint($this->width - $shadow->right - 1, $this->height - $shadow->bottom - 1);
		
			// Draw image background
			$this->drawer->filledRectangle($this->background, new awLine($p1, $p2));
			$this->background->free();
			
			// Draw image border
			$this->border->rectangle($this->drawer, $p1, $p2);
			
		}
		
	}
	
	/**
	 * Draw a component on the image
	 *
	 * @var &$component A component
	 */
	 function drawComponent(&$component) {
		
		$shadow = $this->shadow->getSpace(); // Image shadow
		$border = $this->border->visible() ? 1 : 0; // Image border size
	
		$drawer = $this->drawer;
		$drawer->setImageSize(
			$this->width - $shadow->left - $shadow->right - $border * 2,
			$this->height - $shadow->top - $shadow->bottom - $border * 2
		);
	
		// No absolute size specified
		if($component->w === NULL and $component->h === NULL) {
		
			list($width, $height) = $drawer->setSize($component->width, $component->height);
	
			// Set component size in pixels
			$component->setAbsSize($width, $height);
			
		} else {
		
			$drawer->setAbsSize($component->w, $component->h);
		
		}
		
		if($component->top !== NULL and $component->left !== NULL) {
			$drawer->setAbsPosition(
				$border + $shadow->left + $component->left,
				$border + $shadow->top + $component->top
			);
		} else {
			$drawer->setPosition($component->x, $component->y);
		}
		
		$drawer->movePosition($border + $shadow->left, $border + $shadow->top);
		
		list($x1, $y1, $x2, $y2) = $component->getPosition();
		
		$component->init($drawer);
		
		$component->drawComponent($drawer, $x1, $y1, $x2, $y2, $this->antiAliasing);
		$component->drawEnvelope($drawer, $x1, $y1, $x2, $y2);
		
		$component->finalize($drawer);
	
	}
	
	 function drawShadow() {
	
		$drawer = $this->getDrawer();
		
		$this->shadow->draw(
			$drawer,
			new awPoint(0, 0),
			new awPoint($this->width, $this->height),
			SHADOW_IN
		);
	
	}
	
	/**
	 * Send the image into a file or to the user browser
	 *
	 * @var string $return If set to true, this method will return the image instead of outputing it
	 * @var string $header Enable/disable sending content-type header
	 */
	 function send($return = FALSE, $header = TRUE) {
	
		// Test if format is available
		if((imagetypes() & $this->format) === FALSE) {
			awImage::drawError("Class Image: Format '".$this->format."' is not available on your system. Check that your PHP has been compiled with the good libraries.");
		}
	
		// Get some infos about this image
		
		switch($this->format) {
			case IMAGE_JPEG :
				$function = 'imagejpeg';
				break;
			case IMAGE_PNG :
				$function = 'imagepng';
				break;
			case IMAGE_GIF :
				$function = 'imagegif';
				break;
		}
		
		// Create image
	
		// Send headers to the browser
		if($header === TRUE and headers_sent() === FALSE) {
			header("Content-type: image/".$this->getFormat());
		}
		
		if($return) {
			ob_start();
		}
		
		$function($this->resource);
		
		if($return) {
			return ob_get_clean();
		}
	
	}
	

	/*
	 * Display an error image and exit
	 *
	 * @param string $message Error message
	 */
	  function drawError($message) {
	
		 
		static $errorWriting;
		
	
		if($errorWriting) {
			return;
		}
	
		$errorWriting = TRUE;
	
		$message = wordwrap($message, 40, "\n", TRUE);
		
		$width = 400;
		$height = max(100, 40 + 22.5 * (substr_count($message, "\n") + 1));
		
		$image = new awImage();
		$image->setSize($width, $height);
		
		$drawer = $image->getDrawer();
		
		// Display title
		$drawer->filledRectangle(
			new awWhite,
			new awLine(
				new awPoint(0, 0),
				new awPoint($width, $height)
			)
		);
		
		$drawer->filledRectangle(
			new awRed,
			new awLine(
				new awPoint(0, 0),
				new awPoint(110, 25)
			)
		);
		
		$text = new awText(
			"Artichow error",
			new awFont3,
			new awWhite,
			0
		);
		
		$drawer->string($text, new awPoint(5, 6));
		
		// Display red box
		$drawer->rectangle(
			new awRed,
			new awLine(
				new awPoint(0, 25),
				new awPoint($width - 90, $height - 1)
			)
		);
		
		// Display error image
		$file = ARTICHOW_IMAGE.DIRECTORY_SEPARATOR.'error.png';
		
		$imageError = new awFileImage($file);
		$drawer->copyImage(
			$imageError,
			new awPoint($width - 81, $height - 81),
			new awPoint($width - 1, $height - 1)
		);
		
		// Draw message
		$text = new awText(
			$message,
			new awFont2,
			new awBlack,
			0
		);
		
		$drawer->string($text, new awPoint(10, 40));
		
		$image->send();
		
		exit;
	
	}
	
	/*
	 * Display an error image located in a file and exit
	 *
	 * @param string $error Error name
	 */
	  function drawErrorFile($error) {
	
		$file = ARTICHOW_IMAGE.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR.$error.'.png';
		
		header("Content-Type: image/png");
		readfile($file);
		exit;
	
	}
	
	 function getFormat() {
		
		switch($this->format) {
			case IMAGE_JPEG :
				return 'jpeg';
			case IMAGE_PNG :
				return 'png';
			case IMAGE_GIF :
				return 'gif';
		}
		
	}

}

registerClass('Image');

 
/**
 * Load an image from a file
 *
 * @package Artichow
 */
class awFileImage extends awImage {

	/**
	 * Build a new awimage
	 *
	 * @param string $file Image file name
	 */
	 function awFileImage($file) {
	
		$image = @getimagesize($file);
		
		if($image and in_array($image[2], array(2, 3))) {
		
			$this->setSize($image[0], $image[1]);
			
			switch($image[2]) {
			
				case 2 :
					$this->resource = imagecreatefromjpeg($file);
					break;
			
				case 3 :
					$this->resource = imagecreatefrompng($file);
					break;
			
			}
		
			$this->drawer = new awDrawer($this->resource);
			$this->drawer->setImageSize($this->width, $this->height);
			
		} else {
			awImage::drawError("Class FileImage: Artichow does not support the format of this image (must be in PNG or JPEG)");
		}
	
	}

}

registerClass('FileImage');

/*
 * Check for GD2
 */
if(function_exists('imagecreatetruecolor') === FALSE) {
	awImage::drawErrorFile('missing-gd2');
}
?>
