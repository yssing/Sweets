<?php

class textcontrol {

	public static function indexAction($args){	
		if ($args){
			list($id,$key,$headline,$bodytext) = text::readText($args[0]);
			$return = '<div id="text_'.$id.'">';
			$return .= text::getMetaData($id);		
			$return .= $bodytext;
			$return .= '</div>';
			$bodytext = $return;
			
			// if the article is not found, we throw a 404
			if (!$id){
				route::error(404);
			}		
		} else {
			route::error(404);
		}
		template::initiate('main');
			template::header($headline);
			template::body($bodytext);
			template::title(text::readTextByKey('TITLE'));
			template::footer(text::readTextByKey('FOOTER'));
			template::replace('[COPY]',text::readTextByKey('COPY'));	
			template::replace('[MENU]',menu::makeMenu());				
		template::end();
	}
	
	public static function getTextByKeyAction($args){	
		$bodytext = text::readTextByKey($args[0]);
	
		template::initiate('base');
			template::header('');			
			template::body($bodytext);			
		template::end();
	}
	
	public static function sendMailAction($args){
		include_once('system/utils/mailer.class.php');
		$sendmail = language::readType('SENDMAIL');
		$fieldset = array("style" => "width:80%;margin:auto;");

		if ($_SESSION['security_code']
			&& $_SESSION['security_code'] == $args['captcha']
			&& $args['receiver_mail'] 
			&& $args['receiver_text'] 
			&& $args['sender_mail']
			&& $args['sender_name']){ 

			list($id,$key,$head,$bodyText) = text::readText($args['textid']);
			
			mailer::sendMailToUser($args['sender_name'],$args['sender_mail'],$args['receiver_mail'],$args['receiver_text'],$bodyText);
			$body = form::fieldset('sendmail',language::readType('MAIL_WAS_SEND'),'',$fieldset);			
		} else {
			$body = form::beginField('sendmail',$sendmail,$fieldset);
				$body .= form::beginForm('Send','modules/cms/text/send_mail');					
					$body .= form::input($args['receiver_mail'],'receiver_mail',TEXT,array('placeholder' => language::readType('RECEIVER_MAIL'))).'<br>';
					$body .= form::input($args['receiver_text'],'receiver_text',TEXT,array('placeholder' => language::readType('RECEIVER_TEXT'))).'<br>';
					$body .= form::input($args['sender_name'],'sender_name',TEXT,array('placeholder' => language::readType('SENDER_NAME'))).'<br>';
					$body .= form::input($args['sender_mail'],'sender_mail',TEXT,array('placeholder' => language::readType('SENDER_MAIL'))).'<br>';

					$body .= form::inputControl('','captcha','<img src="/common/query/captcha" />',
					array('placeholder' => language::readType('REPEAT_CAPTCHA'), 'style' => 'height:50px')).'<br>';					
					
					$body .= form::input($args['textid'],'textid',HIDDEN);
					$body .= form::submit('Send mail','button',0,array("onclick" => "mailtext()"));
				$body .= form::endForm('Send',false);
			$body .= form::endField();
		}

		template::initiate('base');
			template::noCache();
			template::header($sendmail);
			template::body($body);
		template::end();		
	}

	public static function listAction($args){	
		if (!user::validateAdmin()){
			route::error(403);
		}
		$searchVal = '';
		if (isset($args['searchfield'])){
			$searchVal = $args['searchfield'];
		}			
		$body = views::displayEditListview(text::listText($searchVal));
		
		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();	
	}
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		text::destroyText($args[0]);
		textrevision::destroyRevisions($args[0]);
		textmeta::destroyMetaData($args[0]);
		route::redirect('modules/cms/text/list');
	}
	
	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Headline" => "varchar(100)",
			"BodyText" => "text",
			"TextKey" => "varchar(45)",
			"DateOnline" => "datetime",
			"DateOffline" => "datetime");
		$result = $databaseadmin->createTable('cms_text',$what,"PK_TextID");
		// metadata
		$databaseadmin = new databaseadmin();
		$what = array(
			"Speech" => "tinyint(1)",
			"Mail" => "tinyint(1)",
			"PDF" => "tinyint(1)",
			"Print" => "tinyint(1)",
			"FK_TextID" => "int(10)");
		$result = $databaseadmin->createTable('cms_text_meta',$what,"PK_MetaTextID");		
		// text revisions
		$databaseadmin = new databaseadmin();
		$what = array("Headline" => "varchar(100)",
			"BodyText" => "text",
			"TextKey" => "varchar(45)",
			"FK_TextID" => "int(10)");
		$result = $databaseadmin->createTable('cms_text_revision',$what,"PK_TextRevisionID");		
	}	

	public static function deleteRevisionsAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		textrevision::destroyRevisions($args[0]);
		route::redirect('modules/cms/text/edit/'.$args[0]);
	}	
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error('403');
		}
		if (isset($args[0]) && $args[0]){
			list($id,$key,$headline,$bodytext,$language) = text::readText($args[0]);
			$revisions = views::displayListview(textrevision::listRevisions($args[0]),'modules/cms/text/revision');
			list($speech,$mail,$pdf,$print) = textmeta::readMetaData($args[0]);
		} else {
			$id = 0;
			$editkey = '';
			$headline = '';
			$bodytext = '';
			$revisions = '';
			$key = '';
			$speech = $mail = $pdf = $print = 0;
			$language = language::get();
		}

		$body = form::beginForm('update','modules/cms/text/update');
			$body .= form::fieldset('field1','<h3>'.language::readType('KEY').'</h3>',form::input($key,'key',TEXT));
			$body .= form::fieldset('field2','<h3>'.language::readType('LANGUAGE').'</h3>',form::input($language,'language',TEXT));
			$body .= form::fieldset('field3','<h3>'.language::readType('HEADLINE').'</h3>',form::input($headline,'headline',TEXT));
			$body .= form::fieldset('field4','<h3>'.language::readType('TEXT').'</h3>',form::textarea($bodytext,'bodytext'));
			
			$metaForm  = '<b>'.language::readType('SPEECH').'</b> '.form::check($speech,'speech');
			$metaForm .= '<b>'.language::readType('MAIL').'</b> '.form::check($mail,'mail');
			$metaForm .= '<b>'.language::readType('PDF').'</b> '.form::check($pdf,'pdf');
			$metaForm .= '<b>'.language::readType('PRINT').'</b> '.form::check($print,'print');
			
			$body .= form::fieldset('field5','<h3>'.language::readType('META').'</h3>',$metaForm).'<br />';
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');
		$body .= '<br><br><br><h3>'.language::readType('REVISIONS').'</h3>';
		$body .= $revisions;
		$body .= form::warnButton('/modules/cms/text/delete_revisions/'.$args[0],language::readType('DELETE_REVISIONS'));
		
		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}
	
	public static function editTextAction($args){
		if (!user::validateAdmin()){
			route::error('403');
		}
		$body = '';
		
		if (isset($args['id']) && $args['id']){
			list($id,$key,$headline,$bodytext,$language) = text::readText($args['id']);
		} else {
			$id = 0;
			$headline = $bodytext = $key = '';
			$language = language::get();
		}
		
		$body = form::beginForm('update','/modules/cms/text/update_text');
			$body .= form::textarea($bodytext,'bodytext',array("style" => "width:900px;height:420px;"));
			$body .= form::input($id,'id',HIDDEN);
			$body .= form::newButton('javascript:updateText('.$id.')',language::readType('UPDATE'));
		$body .= form::endForm('update',0);

		template::initiate('edit');
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}	
	
	public static function updateTextAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}	
		//if (form::validate('update')){
			if ($args['id'] && $args['bodytext']){
				list($id,$key,$headline,$bodytext,$language) = text::readText($args['id']);
				textrevision::createRevision($headline,$bodytext,$key,$language,$args['id']);
				text::updateText($args['id'],$key,$headline,$args['bodytext'],$language);
			} 
		//}
		self::editTextAction($args);
	}
	
	public static function revertAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		list($headline,$bodytext,$createdate,$id) = textrevision::readRevision($args[0]);

		list($fid,$key,$hl,$bt,$language) = text::readText($id);
		
		textrevision::createRevision($hl,$bt,$key,$language,$id);
		
		text::updateText($id,$key,$headline,$bodytext,$language);

		route::redirect('modules/cms/text/edit/'.$id);
	}
	
	public static function deleteRevertAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		list($a,$a,$a,$id) = textrevision::readRevision($args[0]);		
		textrevision::destroyRevision($args[0]);
		route::redirect('modules/cms/text/edit/'.$id);
	}	
	
	public static function revisionAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		list($headline,$bodytext,$createdate) = textrevision::readRevision($args[0]);
		$body = '<div class="border">'.$headline.' <br />'.$createdate.'</div>';
		$body .= '<br /><div class="border">'.$bodytext.'</div><br>';
		$body .= form::newButton('/modules/cms/text/revert/'.$args[0],language::readType('REVERT'));
		$body .= '<br><br><br>';
		$body .= form::warnButton('/modules/cms/text/delete_revert/'.$args[0],language::readType('DELETE'));
		
		template::initiate('admin');
			template::header(language::readType('REVISION'));
			template::body($body);
		template::end();
	}
	
	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}	
		if (form::validate('update')){
			if ($args['id']){
				list($id,$key,$headline,$bodytext,$language) = text::readText($args['id']);
				textrevision::createRevision($headline,$bodytext,$key,$language,$id);
				text::updateText($args['id'],$args['key'],$args['headline'],$args['bodytext'],$args['language']);
				$textid = $args['id'];
			} else {			
				$textid = text::createText($args['key'],$args['headline'],$args['bodytext'],$args['language']);
			}
			
			$speech = isset($args['speech']) ? 1:0;
			$mail = isset($args['mail']) ? 1:0;
			$pdf = isset($args['pdf']) ? 1:0;
			$print = isset($args['print']) ? 1:0;
			
			textmeta::updateMetaData($textid, $speech, $mail, $pdf, $print);
		}
		route::redirect('modules/cms/text/edit/'.$textid);
	}	
}
?>