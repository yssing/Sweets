<?php

class adminmenu_slidein {
	
	public static function indexAction($args){
		if (user::validateAdmin()){
			if (isset($args['articleid']) && $args['articleid']){
				$articleid = '<a href="'.PATH_WEB.'/modules/cms/text/edit/'.$args['articleid'].'" target="_blank">'.language::readType('EDIT_ARTICLE').'</a>';
			} else {
				$articleid = '';
			}

			if (isset($args['editmode']) && $args['editmode'] == 1){
				$edit = '<a href="'.$args['docurl'].'/?editmode=0">'.language::readType('EDIT_MODE_OFF').'</a>';
				$title = '<a href="javascript:editElement(\''.template::path().'\',\''.'[TITLE]'.'\',\'\',0)">'.language::readType('TITLE').'</a>';
			} else {
				$edit = '<a href="'.$args['docurl'].'/?editmode=1">'.language::readType('EDIT_MODE_ON').'</a>';
				$title = '';
			}			
			
			echo '<div class="editorial" id="editorial" onmouseover="showAdminMenu()" onmouseout="hideAdminMenu()">
				<div class="edit_buttons" id="edit_buttons">
				'.$edit.'
				'.$articleid.'			
				'.$title.'
				<a href="'.PATH_WEB.'/?clearcache=1">'.language::readType('CLEAR_CACHE').'</a>
				<a href="'.PATH_WEB.'/admin/login" target="_blank">'.language::readType('ADMIN').'</a>
				<a href="'.PATH_WEB.'" target="_blank">'.language::readType('FRONTPAGE').'</a>
				<br />
				<a href="'.PATH_WEB.'/admin/logout">'.language::readType('LOGOUT').'</a>
				</div>
				<div class="editorial_admin">
					'.language::readType('ADMIN').'
				</div>
			</div>';
		}
	}
}	
?>