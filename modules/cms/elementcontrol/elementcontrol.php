<?php
class elementcontrol{
	public static function indexAction(){
		route::error(403);	
	}
	
	public static function listAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}
	
		$body = views::displayEditListview(element::listElements());
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}

	public static function editAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		
		list($id,$bodytext,$path) = element::readElement($args[0]);	
		$body = form::beginForm('update','modules/cms/element/update');
		$body .= form::fieldset('field1','<h3>'.language::readType('PATH').'</h3>',form::input($path,'path',TEXT));
		$body .= form::fieldset('field2','<h3>'.language::readType('TEXT').'</h3>',form::textarea($bodytext,'bodytext',array("style" => "width:900px;height:620px;"))).'<br />';
		$body .= form::input($id,'id',2);	
		$body .= form::endForm('update');

		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));	
			template::body($body);	
		template::end();	
	}	
	
	public static function editPathAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}	
		$bodytext = element::readElementPath($args['path'],$args['element']);
		
		$body = form::beginForm('update','modules/cms/element/updatePath');
		$style = array("style" => "width:920px;border:0px;");
		$body .= form::fieldset('field1','',form::textarea($bodytext,'bodytext',array("style" => "width:900px;height:420px;")),$style).'<br />';
		$body .= form::input($args['path'],'path',2);
		$body .= form::input($args['element'],'element',2);	
		$body .= form::endForm('update');

		template::initiate('edit');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
			template::replace('[PATH]',PATH_WEB);
		template::end();	
	}		
	
	public static function updateAction($args){	
		if(!user::validateAdmin()){
			route::error(403);
		}

		if($args['id'] && form::validate('update')){
			element::updateElement($args['id'],$args['bodytext'],$args['path']);
		} 
		route::redirect('cms/element/edit/'.$args['id']);
	}
	
	public static function updatePathAction($args){	
		if(!user::validateAdmin()){
			route::error(403);
		}
		
		if(form::validate('update')){
			if(element::doesExist($args['path'],$args['element'])){
				element::updateElementPath($args['path'],$args['bodytext'],$args['element']);
			} else {
				element::createElement($args['path'],$args['bodytext'],$args['element']);
			}
		}
		route::redirect($args['path']);
	}	
	
	public static function installAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Path" => "varchar(100)","BodyText" => "text","Element" => "varchar(45)");
		$result = $databaseadmin->createTable('cms_element',$what,"PK_ElementID");
	}	
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		element::destroyElement($args[0]);
		route::redirect('cms/element/list');
	}
}
?>