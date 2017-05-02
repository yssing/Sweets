<?php

class recover_login{
	public static function indexAction(){
		$body = form::beginForm('findlogin');
			$body .= form::beginField('field1','Så indtast din e-mail her under:',array("style" => "border:0px;float:center;"));
				$body .= form::input('','email',0,array("placeholder" => "E-Mail","style" => "width:300px; float:left"));	
				$body .= form::submit('Find login','button',0,array("style" => "float:right;", "onclick" => "showRecoverLoginReceiver($('#email').val())"));
			$body .= form::endField();
		$body .= form::endForm('findlogin',false);
		
		$javascript = "function showRecoverLoginReceiver(email){
			$.get('/common/recover_login/find/'+email, function(data) {
				showLightBox(data,420,160);
			});
		}";	
		
		$headline = '<span style="visibility:hidden">Glemt dit kodeord?</span>';
		
		template::initiate('base');
			template::noCache();
			template::body($body);
			template::header($headline);
			template::injectJavascript($javascript);
		template::end();
	}
	
	public static function findAction($args){
		include_once('system/utils/mailer.class.php');	
		if ($args[0] && validation::isEMail($args[0])){
			$tmp = user::readUserCredentials($args[0]);
			if (count($tmp) == 1){
				if (!$tmp){
					$body = "E-Mail findes ikke";
				}
			} else {
				list($login,$userid,$uid) = $tmp;
				$returnmessage = 'Hvis du ikke har bedt om at forny dit kodeord, så bare slet den her mail<br />';
				$returnmessage .= 'Dit login er: '.$login.'<br />';
				$returnmessage .= '<a href="'.PATH_WEB.'/common/recover_login/update/'.$uid.'/'.$userid.'">Nyt kodeord</a>';
				mailer::sendMailToUser(TITLE,SITEMAIL,$login,'Dit nye password',$returnmessage);
				
				$body = "Der er blevet sendt en mail.<br />Med et link til at skabe et nyt kodeord.";
			}
		} else {
			$body = "Mangler der noget?";
		}		
		
		$headline = '<span style="visibility:hidden;position:absolute">Glemt dit kodeord?</span>';
		
		template::initiate('base');
			template::noCache();
			template::body($body);
			template::header($headline);
		template::end();
	}
	
	public static function updateAction($args){
		if (isset($args[0]) && isset($args[1])){
			if (intval(user::readUserID($args[0])) == $args[1]){
				$userPassword = baseclass::generateRandStr(6);
				user::updateUserPassword($userPassword,$args[1]);
				$body = "Dit nye kodeord er: ".$userPassword." <br /> Du kan ændre det når du logger ind.";
			} else {
				$body = "ID og UniktID passer ikke sammen.";
			}
		} else {
			$body = "Mangler der noget?";
		}	
		
		template::initiate('main');
			template::noCache();
			template::header('Gendan kodeord');
			template::body($body);
			template::replace('[MENU]',menu::makeMenu());
		template::end();
	}
}
?>