<?php
class lists{
	public static function linkAction(){
		$text = new text();

		$output = '';
		$output .= 'var tinyMCELinkList = new Array(';
		$textarray = $text->listText();
		if(is_array($textarray)){
			foreach($textarray as $line){
				$output .= '["'.$line[1].'","'.PATH_WEB.'/cms/article/'.$line[0].'"],';									
			}
		} 

		$output .= '["show login popup","javascript:showAdminLogin()"],';
		$output .= '["show recover login","javascript:showRecoverLogin()"],';
		$output .= '["News","'.PATH_WEB.'/cms/news/"]';
		$output .= '["Base","/"]';
		$output .= ');';

		// Make output a real JavaScript file!
		header('Content-type: text/javascript');
		// prevent browser from caching
		header('pragma: no-cache');
		header('expires: 0'); // i.e. contents have already expired
		echo $output;			
	}
	
	public static function templateAction(){
		// empty for now, might be included in the future, might.
	}

	public static function mediaAction(){
		$directory = "uploads/files";

		$output = '';
		$output .= 'var tinyMCEMediaList = new Array(';
		if (is_dir($directory)) {
			if ($handle = opendir($directory)) {
				while (false !== ($file = readdir($handle))) {
					if($file != '.' && $file != '..'){
						$output .= '["'.utf8_encode($file).'", "'.utf8_encode("$directory/$file").'"],';								
					}
				}
				closedir($handle);
			}
			$output = substr($output, 0, -1); // remove last comma from array item list (breaks some browsers)
		} 
		$output .= ');';

		// Make output a real JavaScript file!
		header('Content-type: text/javascript');
		// prevent browser from caching
		header('pragma: no-cache');
		header('expires: 0'); // i.e. contents have already expired
		echo $output;
	}

	public static function iconAction(){
		// for now, look in the iconlist.php file
	}
	
	public static function imageAction(){
		$directory = "uploads/images";
		
		$output = '';
		$output .= 'var tinyMCEImageList = new Array(';
		if (is_dir($directory)) {
			if ($handle = opendir($directory)) {
				while (false !== ($file = readdir($handle))) {
					if($file != '.' && $file != '..'){
						$output .= '["'.utf8_encode($file).'", "'.utf8_encode("$directory/$file").'"],';								
					}
				}
				closedir($handle);
			}
			$output = substr($output, 0, -1);
		} 
		$output .= ');';

		// Make output a real JavaScript file!
		header('Content-type: text/javascript');
		// prevent browser from caching
		header('pragma: no-cache');
		header('expires: 0'); // i.e. contents have already expired
		echo $output;	
	}
}
?>