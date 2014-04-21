<?php
class recover_login{
	public static function indexAction(){
		$body = form::beginForm('findlogin');
			$body .= form::beginField('field1','Så indtast din e-mail her under:',array("style" => "width:320px;border:0px;"));
				$body .= form::input('email','email',0,array("onClick" => "removetext(this.id)","style" => "width:300px"));	
				$body .= form::submit('Find login','button',0,array("onclick" => "showRecoverLoginReceiver($('#email').val())"));
			$body .= form::endField();
		$body .= form::endForm('findlogin',false);
		
		$javascript = "function showRecoverLoginReceiver(email){
			$.get('common/recover_login/find/'+email, function(data) {
				showLightBox(data,400,140);
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
		if($args[0] && validation::IsMail($args[0])){
			$tmp = user::readUserCredentials($args[0]);
			if(count($tmp) == 1){
				if(!$tmp){
					$body = "E-Mail findes ikke";
				}
			} else {
				list($login,$userid,$uid) = $tmp;
				$returnmessage = 'Hvis du ikke har bedt om at forny dit kodeord, så bare slet den her mail<br />';
				$returnmessage .= 'Dit login er: '.$login.'<br />';
				$returnmessage .= '<a href="'.PATH_WEB.'/common/recover_login/update/'.$uid.'/'.$userid.'">Nyt kodeord</a>';
				user::sendMailToUser(SITENAME,SITEMAIL,$_REQUEST['id'],'Dit nye password',$returnmessage);
				
				$body = "Der er blevet sendt en mail.<br />Med et link til at genere ny kode.";
			}
		} else {
			$body = "Mangler der noget?";
		}		
		
		$headline = '<span style="visibility:hidden">Glemt dit kodeord?</span>';
		
		template::initiate('base');
			template::noCache();
			template::body($body);
			template::header($headline);
		template::end();
	}
	
	public static function updateAction($args){
		if(isset($args[0]) && isset($args[1])){
			if(intval(user::readUserID($args[0])) == $args[1]){
				$userPassword = user::generateRandStr(6);
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