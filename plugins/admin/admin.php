<?php

class admin {
	
	public static function indexAction($args){
		if(user::validateAdmin()){
			if(isset($args[0]) && $args[0]){
				$articleid = '<a href="'.PATH_WEB.'/cms/articlecontrol/edit/'.$args[0].'" target="_blank">'.language::readType('EDITARTICLE').'</a>';
			} else {
				$articleid = '';
			}
			
			if(isset($args['editmode']) && $args['editmode']){
				$edit = '<a href="?editmode=0">'.language::readType('EDITMODEOFF').'</a>';
				$title = '<a href="javascript:editElement(\''.template::path().'\',\''.'[TITLE]'.'\',\'\',0)">Title</a>';
			} else {
				$edit = '<a href="?editmode=1">'.language::readType('EDITMODEON').'</a>';
				$title = '';
			}			
			
			echo '<div class="editorial" id="editorial" onmouseover="showAdminMenu()" onmouseout="hideAdminMenu()">
				<div class="edit_buttons" id="edit_buttons">
				'.$edit.'
				'.$articleid.'			
				'.$title.'
				<a href="'.PATH_WEB.'/?clearcache=1">'.language::readType('CLEARCACHE').'</a>
				<a href="'.PATH_WEB.'/system/login" target="_blank">'.language::readType('ADMIN').'</a>
				<br />
				<a href="'.PATH_WEB.'/system/login/logout">'.language::readType('LOGOUT').'</a>
				</div>
				<div class="editorial_admin">
					'.language::readType('ADMIN').'
				</div>
			</div>';
		}
	}
}	
?>