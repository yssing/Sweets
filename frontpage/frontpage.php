<?php

/**
 * This is used to control how the frontpage loads
 */

class frontpage {
	public static function indexAction(){		
		include_once('template/'.TEMPLATE.'/frontpage/template_frontpage.php');		
		template_frontpage::indexAction();	
	}
}
?>