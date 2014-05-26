<?php
class questioncontrol{
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
		$body .= views::displayEditListview(question::listQuestions());
		$body .= form::newButton();		
		
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
		
		list($id, $question, $groupid, $min, $max) = question::readQuestion($args[0]);

		$body = form::beginForm('update','modules/jobspecialisten/question/update');
			$body .= form::fieldset('field1','<h3>'.language::readType('GROUP').'</h3>',form::select(questiongroup::listGroups(),$groupid,'groupid',1));
			$body .= form::fieldset('field2','<h3>'.language::readType('MIN').'</h3>',form::input($min,'min',TEXT));		
			$body .= form::fieldset('field3','<h3>'.language::readType('MAX').'</h3>',form::input($max,'max',TEXT));		
			$body .= form::fieldset('field4','<h3>'.language::readType('QUESTION').'</h3>',form::textarea($question,'question')).'<br />';
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
				question::updateQuestion($args['id'],$args['question'],$args['groupid'],$args['min'],$args['max']);
			} else {			
				question::createQuestion($args['question'],$args['groupid'],$args['min'],$args['max']);
			}
		}
		route::redirect('modules/jobspecialisten/question/list');
	}
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		question::destroyQuestion($args[0]);
		route::redirect('modules/jobspecialisten/question/list');
	}
	
	public static function installAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Question" => "text",
			"FK_QuestionGroupID" => "int(10)",
			"Min" => "int(10)",
			"Max" => "int(10)",
			"Type" => "varchar(45)");
		$result = $databaseadmin->createTable('js_questions',$what,"PK_QuestionID");
		
		$databaseadmin = new databaseadmin();
		$what = array("Answer" => "int(10)", "AnswerGroup" => "int(10)", "FK_QuestionID" => "int(10)");
		$result = $databaseadmin->createTable('js_answers',$what,"PK_AnswerID");		
	}		
}

?>