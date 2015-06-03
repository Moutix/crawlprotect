<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */
 
require_once dirname(__FILE__)."/../Graph.class.php";

 
/**
 * Built-in PHP fonts
 *
 * @package Artichow
 */
class awFont {
	
	/**
	 * Used font
	 * 
	 * @param int $font
	 */
	var $font;
	
	/**
	 * Build the font
	 *
	 * @param int $font Font identifier
	 */
	 function awFont($font) {
	
		$this->font = $font;
	
	}
	
	/**
	 * Draw a text
	 *
	 * @param $drawer
	 * @param $p Draw text at this point
	 * @param &$text The text
	 * @param &$width Text box width
	 */
	 function draw($drawer, $p, &$text, $width = NULL) {
	
		$angle = $text->getAngle();
	
		if($angle !== 90 and $angle !== 0) {
			awImage::drawError("Class Font: You can only use 0° and 90° angles.");
		}
		
		if($angle === 90) {
			$function = 'imagestringup';
		} else {
			$function = 'imagestring';
		}
		
		if($angle === 90) {
			$addAngle = $this->getTextHeight($text);
		} else {
			$addAngle = 0;
		}
	
		$color = $text->getColor();
		$rgb = $color->getColor($drawer->resource);
		
		$textString = $text->getText();
		$textString = str_replace("\r", "", $textString);
		$textHeight = $this->getTextHeight($text);
		
		// Split text if needed
		if($width !== NULL) {
		
			$characters = floor($width / ($this->getTextWidth($text) / strlen($textString)));
			
			$textString = wordwrap($textString, $characters, "\n", TRUE);
		
		}
		
		$lines = explode("\n", $textString);
		
		foreach($lines as $i => $line) {
		
			// Line position handling
			if($angle === 90) {
				$addX = $i * $textHeight;
				$addY = 0;
			} else {
				$addX = 0;
				$addY = $i * $textHeight;
			}
		
			$function(
				$drawer->resource,
				$this->font,
				$drawer->x + $p->x + $addX,
				$drawer->y + $p->y + $addY + $addAngle,
				$line,
				$rgb
			);
			
		}
	
	}
	
	/**
	 * Get the width of a string
	 *
	 * @param &$text A string
	 */
	 function getTextWidth(&$text) {
	
		if($text->getAngle() === 90) {
			$text->setAngle(45);
			return $this->getTextHeight($text);
		} else if($text->getAngle() === 45) {
			$text->setAngle(90);
		}
		
		$font = $text->getFont();
		$fontWidth = imagefontwidth($font->font);
		
		if($fontWidth === FALSE) {
			awImage::drawError("Class Font: Unable to get font size.");
		}
		
		return (int)$fontWidth * strlen($text->getText());
	
	}
	
	/**
	 * Get the height of a string
	 *
	 * @param &$text A string
	 */
	 function getTextHeight(&$text) {
	
		if($text->getAngle() === 90) {
			$text->setAngle(45);
			return $this->getTextWidth($text);
		} else if($text->getAngle() === 45) {
			$text->setAngle(90);
		}
		
		$font = $text->getFont();
		$fontHeight = imagefontheight($font->font);
		
		if($fontHeight === FALSE) {
			awImage::drawError("Class Font: Unable to get font size.");
		}
		
		return (int)$fontHeight;

	}

}

registerClass('Font');

/**
 * TTF fonts
 *
 * @package Artichow
 */
class awTTFFont extends awFont {

	/**
	 * Font size
	 *
	 * @var int
	 */
	var $size;

	/**
	 * Font file
	 *
	 * @param string $font Font file
	 * @param int $size Font size
	 */
	 function awTTFFont($font, $size) {
	
		parent::awFont($font);
		
		$this->size = (int)$size;
	
	}
	
	/**
	 * Draw a text
	 *
	 * @param $drawer
	 * @param $p Draw text at this point
	 * @param &$text The text
	 * @param &$width Text box width
	 */
	 function draw($drawer, $p, &$text, $width = NULL) {
	
		// Make easier font positionment
		$text->setText($text->getText()." ");
	
		$color = $text->getColor();
		$rgb = $color->getColor($drawer->resource);
		
		$box = imagettfbbox($this->size, $text->getAngle(), $this->font, $text->getText());
		
		$textHeight =  - $box[5];
		
		$box = imagettfbbox($this->size, 90, $this->font, $text->getText());
		$textWidth = abs($box[6] - $box[2]);
	
		// Restore old text
		$text->setText(substr($text->getText(), 0, strlen($text->getText()) - 1));
		
		$textString = $text->getText();
		
		// Split text if needed
		if($width !== NULL) {
		
			$characters = floor($width / $this->getAverageWidth());
			$textString = wordwrap($textString, $characters, "\n", TRUE);
		
		}
		
		imagettftext(
			$drawer->resource,
			$this->size,
			$text->getAngle(),
			$drawer->x + $p->x + $textWidth  * sin($text->getAngle() / 180 * M_PI),
			$drawer->y + $p->y + $textHeight,
			$rgb,
			$this->font,
			$textString
		);
		
	}
	
	/**
	 * Get the width of a string
	 *
	 * @param &$text A string
	 */
	 function getTextWidth(&$text) {
		
		$box = imagettfbbox($this->size, $text->getAngle(), $this->font, $text->getText());
		
		if($box === FALSE) {
			awImage::drawError("Class TTFFont: Unable to get font size.");
		}
		
		list(, , $x2, $y2, , , $x1, $y1) = $box;
		
		return abs($x2 - $x1);
	
	}
	
	/**
	 * Get the height of a string
	 *
	 * @param &$text A string
	 */
	 function getTextHeight(&$text) {
		
		$box = imagettfbbox($this->size, $text->getAngle(), $this->font, $text->getText());
		
		if($box === FALSE) {
			awImage::drawError("Class TTFFont: Unable to get font size.");
		}
		
		list(, , $x2, $y2, , , $x1, $y1) = $box;
		
		return abs($y2 - $y1);

	}
	
	/**
	 * Get average width of a character
	 *
	 * @return int
	 */
	 function getAverageWidth() {
	
		$text = "azertyuiopqsdfghjklmmmmmmmwxcvbbbn,;:!?.";
		
		$box = imagettfbbox($this->size, 0, $this->font, $text);
		
		if($box === FALSE) {
			awImage::drawError("Class TTFFont: Unable to get font size.");
		}
		
		list(, , $x2, $y2, , , $x1, $y1) = $box;
		
		return abs($x2 - $x1) / strlen($text);
	
	}

}

registerClass('TTFFont');



$php = '';

for($i = 1; $i <= 5; $i++) {

	$php .= '
	class awFont'.$i.' extends awFont {
	
		function awFont'.$i.'() {
			parent::awFont('.$i.');
		}
	
	}
	';
	
	if(ARTICHOW_PREFIX !== 'aw') {
		$php .= '
		class '.ARTICHOW_PREFIX.'Font'.$i.' extends awFont'.$i.' {
		}
		';
	}

}

eval($php);

$php = '';

foreach($fonts as $font) {

	$php .= '
	class aw'.$font.' extends awTTFFont {
	
		function aw'.$font.'($size) {
			parent::awTTFFont(\''.(ARTICHOW_FONT.DIRECTORY_SEPARATOR.$font.'.ttf').'\', $size);
		}
	
	}
	';
	
	if(ARTICHOW_PREFIX !== 'aw') {
		$php .= '
		class '.ARTICHOW_PREFIX.$font.' extends aw'.$font.' {
		}
		';
	}

}

eval($php);
//mod to be able to use graph without ttf support using  modification propose by Joel Alexandre  http://paradigma.pt/ja/slog/
if(!class_exists('awTuffy'))
{
	class awTuffy extends awFont
	{

		function awTuffy($size)
		{
			$font = 1;

			switch ($size)
			{
				case '7':
				$font = 1;
				break;
				case '10':
				$font = 2;
				break;
				case '16':
				$font = 3;
				break;
			}
			$this->font = $font;
		}
	}
}

?>
