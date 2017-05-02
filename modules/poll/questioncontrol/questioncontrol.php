<?php
class QuestionControl{
	public static function indexAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		$body = views::displayEditListview(question::listQuestions());

		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();			
	}
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		if (isset($args[0])){
			list($id, $question, $groupid, $min, $max, $priority) = question::readQuestion($args[0]);
		} else {
			$id = ''; 
			$question = ''; 
			$groupid = ''; 
			$min = ''; 
			$max = ''; 
			$priority = '';
		}	

		$body = form::beginForm('update','modules/poll/question/update');
			$body .= form::fieldset('field1','<h3>'.language::readType('GROUP').'</h3>',form::select(questiongroup::listGroups(),$groupid,'groupid',1));
			$body .= form::fieldset('field2','<h3>'.language::readType('MIN').'</h3>',form::input($min,'min',TEXT));		
			$body .= form::fieldset('field3','<h3>'.language::readType('MAX').'</h3>',form::input($max,'max',TEXT));		
			$body .= form::fieldset('field4','<h3>'.language::readType('PRIORITY').'</h3>',form::input($priority,'priority',TEXT));		
			$body .= form::fieldset('field5','<h3>'.language::readType('QUESTION').'</h3>',form::textarea($question,'question','style="height:120px;"')).'<br />';
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');	
				
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}
	
	public static function answerAction($args){
		$questionlist = question::listQuestionsOnPoll($args['pollname']);
		foreach($questionlist as $single){
			list($questionid) = $single;
			if (isset($args['question'.$questionid])){
				list($answer) = question::readAnswer($questionid);

				if ($answer){
					question::updateAnswer($questionid,$args['question'.$questionid]);
				} else {
					question::createAnswer($questionid,$args['question'.$questionid]);
				}
			}
		}	
	}
	
	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		if (form::validate('update')){
			if ($args['id']){
				question::updateQuestion($args['id'],$args['question'],$args['groupid'],$args['min'],$args['max'],$args['priority']);
			} else {			
				question::createQuestion($args['question'],$args['groupid'],$args['min'],$args['max'],$args['priority']);
			}
		}
		route::redirect('modules/poll/question/list');
	}
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		question::destroyQuestion($args[0]);
		route::redirect('modules/poll/question/list');
	}
	
	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Question" => "text",
			"FK_QuestionGroupID" => "int(10)",
			"Min" => "int(10)",
			"Max" => "int(10)",
			"Type" => "varchar(45)",
			"Priority" => "int(10)");
		$result = $databaseadmin->createTable('poll_questions',$what,"PK_QuestionID");
		
		$databaseadmin = new databaseadmin();
		$what = array("Answer" => "int(10)", "FK_QuestionID" => "int(10)");
		$result = $databaseadmin->createTable('poll_answers',$what,"PK_AnswerID");		
	}		
}