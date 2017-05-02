<?php
class lists{
	public static function linkAction(){
		$text = new text();

		$output = '';
		$output .= 'var tinyMCELinkList = new Array(';
		$textarray = $text->listText();
		if (is_array($textarray)){
			foreach($textarray as $line){
				$output .= '["'.$line[2].'","'.PATH_WEB.'/cms/article/'.$line[0].'"],';									
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
	
	public static function linkJsonAction(){
		$output = '';
		$output .= '[';
		$textarray = text::listText();
		if (is_array($textarray)){
			foreach($textarray as $line){
				$output .= "{title: '".$line[2]."', value: '".PATH_WEB.'/cms/article/'.$line[0]."'},";					
			}
		} 
		$output .= "{title: 'show login popup', value: 'javascript:showAdminLogin('},";
		$output .= "{title: 'show recover login', value: 'javascript:showRecoverLogin()'},";
		$output .= "{title: 'News', value: '".PATH_WEB."/cms/news/'},";
		$output .= "{title: 'Base', value: '/'},";
		$output .= "{title: 'Files', value: '---'},";
		
		$output .= self::medialist();	

		$output .= ']';	
		echo $output;
	}	
	
	public static function templateAction(){
		// empty for now, might be included in the future, might.
	}

	private static function medialist(){
		$directory = "uploads/files";
		
		$output = '';
		if (is_dir($directory)) {
			if ($handle = opendir($directory)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != '.' && $file != '..'){
						$output .= "{title: '".utf8_encode($file)."', value: '".utf8_encode("/$directory/$file")."'},";							
					}
				}
				closedir($handle);
			}
			$output = substr($output, 0, -1);
		} 
		return $output;
	}
	
	public static function mediaJsonAction(){
		$directory = "uploads/files";
		
		$output = '';
		$output .= '[';
		$output .= self::medialist();
		$output .= ']';	
		echo $output;
	}	
	
	public static function mediaAction(){
		$directory = "uploads/files";

		$output = '';
		$output .= 'var tinyMCEMediaList = new Array(';
		if (is_dir($directory)) {
			if ($handle = opendir($directory)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != '.' && $file != '..'){
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
	
	public static function imageJsonAction(){
		$directory = "uploads/images/full";
		
		$output = '';
		$output .= '[';
		if (is_dir($directory)) {
			if ($handle = opendir($directory)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != '.' && $file != '..'){
						$output .= "{title: '".utf8_encode($file)."', value: '".utf8_encode("/$directory/$file")."'},";
					}
				}
				closedir($handle);
			}
			$output = substr($output, 0, -1);
		} 
		$output .= ']';	
		echo $output;
	}
	
	public static function imageAction(){
		$directory = "uploads/images/full";
		
		$output = '';
		$output .= 'var tinyMCEImageList = new Array(';
		if (is_dir($directory)) {
			if ($handle = opendir($directory)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != '.' && $file != '..'){
						$output .= '["'.utf8_encode($file).'", "'.utf8_encode("/$directory/$file").'"],';								
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