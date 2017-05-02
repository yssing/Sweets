<?php
include_once('system/class/baseclass.class.php');
class captcha extends baseclass{

	public static function testImage(){
		// Create a blank image and add some text
		$im = imagecreatetruecolor(120, 20);
		$text_color = imagecolorallocate($im, 233, 14, 91);
		imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);

		// Set the content type header - in this case image/png
		header('Content-Type: image/png');

		// Output the image
		ImagePng($im);

		// Free up memory
		imagedestroy($im);
	}

	/**
	 * This method generates a captcha image.
	 * The image needs an external Action, found in common/query/captcha
	 *
	 * @param int $length the length of the string in the image.   
	 * @param int $width the width of the image.   
	 * @param int $height the height of the image.   
	 *
	 * @return string $randstr the generated string.
	 *	 
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function createImage($length=6,$width=130,$height=35){

		$security_code = self::generateRandStr($length);
	
		/*
		Use this part if you need to Set the session 
		to store the security code
		*/
		$_SESSION['security_code'] = $security_code;

		//$CodeInd=0;
		$arrSecCode=array();
		$chars = preg_split('//', $security_code); 

		$security_code=implode(" ",$chars);

		//Create the image resource
		$image = ImageCreate($width, $height); 

		//We are making three colors, white, black and gray
		$arrB=array(0,255,129,10,48,200,186);
		$arrR=array(0,255,129,111,48,210,126);
		$arrG=array(0,205,139,110,48,5,186);
		$black = ImageColorAllocate($image, $arrR[rand(0,6)], $arrG[rand(0,6)], $arrB[rand(0,6)]);
		$white = ImageColorAllocate($image, 255, 255, 255);
		$grey = ImageColorAllocate($image, 175, 253, 253);

		//Make the background black
		ImageFill($image, 0, 0, $black);

		$font=5;

		$arrSel=array(1,2,3,4);
		$selectedNum=$arrSel[rand(0,3)];

		ImageString($image, $font, 10, 10, $security_code, $white);

		//Throw in some lines to make it a little bit harder for any bots to break

		ImageRectangle($image,0,0,$width-1,$height-1,$grey);

		if ($selectedNum == 1 ){
			imageline($image, 0, $height/2, $width, $height/5, $grey);
			imageline($image, $width/2, 0, $width/3, $height/5, $grey);
			imageline($image, $width/2, 0, $width/10, $height, $grey);
			imageline($image, $width/2, 0, $width/10, $height/6, $grey);
		}

		if ($selectedNum == 2 ){
			imageline($image, $width/1, 0, $width/6, $height, $grey);
			imageline($image, 0, $height/5, $width, $height/8, $grey);
			imageline($image, 0, $height/5, $width/5, $height/8, $grey);
			imageline($image, 0, $height/3, $width, $height, $grey);
		}

		if ($selectedNum == 3 ){
			imageline($image, 0, $height, $width, 0, $grey);
			imageline($image, 0, 0, $height, $height, $grey);
			imageline($image, $width/5, 0, $width/6, $height, $grey);
			imageline($image, $width/4, 0, $width/4, $height, $grey);
		}
	   
		//Tell the browser what kind of file is come in
		header("Content-Type: image/jpeg");

		//Output the newly created image in jpeg format
		ImageJpeg($image);
	  
		//Free up resources
		ImageDestroy($image);
	}
}
?>