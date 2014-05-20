<?php

class testcontrol{
	public static function indexAction(){
		$dbFac = new dbfactory('cms_news');
		$dbbase = $dbFac->commit();
		var_dump($dbbase);
	}	
}

?>