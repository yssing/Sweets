<?php

class questiongroupcontrol{
	public static function indexAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}	
	}
	
	public static function listAction($args){	
		if(!user::validateAdmin()){
			route::error(403);
		}

		$body = '<div id="edit"></div>';		
		$body .= views::displayEditListview(questiongroup::listGroups());
		$body .= form::newButton();		
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();	
	}	
	
	public static function installAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("GroupName" => "varchar(45)", "Priority" => "int(10)");
		$result = $databaseadmin->createTable('js_question_groups',$what,"PK_QuestionGroupID");			
	}
	
	public static function editAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		
		list($id, $groupname, $priority) = questiongroup::readGroup($args[0]);

		$body = form::beginForm('update','modules/jobspecialisten/questiongroup/update');
			$body .= form::fieldset('field1','<h3>'.language::readType('HEADLINE').'</h3>',form::input($groupname,'groupname',TEXT));
			$body .= form::fieldset('field2','<h3>'.language::readType('PRIORITY').'</h3>',form::input($priority,'priority',TEXT));
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');	
				
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}	
	
	public static function updateAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		
		if(form::validate('update')){
			if($args['id']){
				questiongroup::updateGroup($args['id'],$args['groupname'],$args['priority']);
			} else {			
				questiongroup::createGroup($args['groupname'],$args['priority']);
			}
		}
		route::redirect('modules/jobspecialisten/questiongroup/list');
	}	
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		questiongroup::destroyGroup($args[0]);
		route::redirect('modules/jobspecialisten/questiongroup/list');
	}	
}

?>