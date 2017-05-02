<?php

class pollcontrol{

	public static function indexAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}	
	}
	
	public static function listAction($args){	
		if (!user::validateAdmin()){
			route::error(403);
		}
		$body = views::displayEditListview(poll::listPolls());	
		
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
		
		list($id, $headline, $description, $online, $offline, $key, $usemarkers) = poll::readPoll($args[0]);

		$body = form::beginForm('update','modules/poll/poll/update');
			$body .= form::fieldset('field1',language::readType('HEADLINE'),form::input($headline,'headline',TEXT));

			$body .= form::fieldset('field6',language::readType('ONLINE'),form::inputControl($online,'online','<img src="[TEMPLATE]/icon/calendar.png">'));
			$body .= form::fieldset('field7',language::readType('OFFLINE'),form::inputControl($offline,'offline','<img src="[TEMPLATE]/icon/calendar.png">'));
			
			$body .= form::fieldset('field4',language::readType('KEY'),form::input($key,'key',TEXT));
			$body .= form::fieldset('field5',language::readType('USE_MARKERS'),form::check($usemarkers,'use_markers'));
			$body .= form::fieldset('field6',language::readType('DESCRIPTION'),form::textarea($description,'description')).'<br />';
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');	
				
		$javascript = '
			$(function(){
				$("#online").datetimepicker({format:"Y-m-d H:i:s", step:30});
				$("#offline").datetimepicker({format:"Y-m-d H:i:s", step:30});
			});
		';	
	
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
			template::injectJavascript($javascript);
		template::end();
	}	
	
	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$marker = ($args['use_markers']) ? 1 : 0;
		if (form::validate('update')){
			if ($args['id']){
				poll::updatePoll($args['id'],$args['headline'],$args['description'],$args['online'],$args['offline'],$args['key'],$marker);
			} else {			
				poll::createPoll($args['headline'],$args['description'],$args['online'],$args['offline'],$args['key'],$marker);
			}
		}
		route::redirect('modules/poll/poll/list');
	}

	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array(
			"Headline" => "varchar(100)", 
			"Description" => "text",
			"DateOnline" => "datetime",
			"DateOffline" => "datetime",
			"PollKey" => "varchar(45)",
			"UseMarkers" => "tinyint(1)");
		$result = $databaseadmin->createTable('poll',$what,"PK_PollID");			
	}
}

?>