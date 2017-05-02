<?php

class questiongroupcontrol{
	public static function indexAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}	
	}
	
	public static function listAction($args){	
		if (!user::validateAdmin()){
			route::error(403);
		}
	
		$body = views::displayEditListview(questiongroup::listGroups());	
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();	
	}	
	
	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("GroupName" => "varchar(200)","Grouped" => "varchar(45)", "Priority" => "int(10)", "FK_PollID" => "int(10)");
		$result = $databaseadmin->createTable('poll_question_groups',$what,"PK_QuestionGroupID");			
	}
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		if (isset($args[0])){
			list($id, $groupname, $group, $priority, $pollId) = questiongroup::readGroup($args[0]);
		} else {
			$id = $groupname = $group = $priority = $pollId = '';
		}
		$pollList = poll::listPollsDropDown();
		
		$body = form::beginForm('update','modules/poll/questiongroup/update');
			$body .= form::fieldset('field1',language::readType('HEADLINE'),form::input($groupname,'groupname',TEXT));
			$body .= form::fieldset('field2',language::readType('GROUP'),form::input($group,'group',TEXT));
			$body .= form::fieldset('field3',language::readType('PRIORITY'),form::input($priority,'priority',TEXT));
			$body .= form::fieldset('field4',language::readType('POLLNAME'),form::select($pollList,$pollId,'pollid',1));
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');	
				
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}	
	
	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		if (form::validate('update')){
			if ($args['id']){
				questiongroup::updateGroup($args['id'],$args['groupname'],$args['group'],$args['priority'],$args['pollid']);
			} else {			
				questiongroup::createGroup($args['groupname'],$args['group'],$args['priority'],$args['pollid']);
			}
		}
		route::redirect('modules/poll/questiongroup/list');
	}	
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		questiongroup::destroyGroup($args[0]);
		route::redirect('modules/poll/questiongroup/list');
	}	
}

?>