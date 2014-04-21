<?php
class install{
	
	private static $rootuser = '';
	private static $rootpassword = '';

	public static function indexAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}	
		$PATH_WEB = (PATH_WEB) ? PATH_WEB : 'http://'.$_SERVER['HTTP_HOST'];
		$FRONTPAGE = (FRONTPAGE) ? FRONTPAGE : '';
		$DATABASE = (DATABASE) ? DATABASE : '';
		$DBUSER = (DBUSER) ? DBUSER : '';
		$DBPASSWORD = (DBPASSWORD) ? DBPASSWORD : '';
		$DATABASESERVER = (DATABASESERVER) ? DATABASESERVER : '';
		$ENVIRONMENT = (ENVIRONMENT) ? ENVIRONMENT : 'LIVE';
		$TPREP = (TPREP) ? TPREP : 'bp_';
		$STD_LANGUAGE = (STD_LANGUAGE) ? STD_LANGUAGE : 'DK';
		$ERROR_REPORT = (ERROR_REPORT) ? ERROR_REPORT : 0;
		$LOG_ERROR = (LOG_ERROR) ? LOG_ERROR : 0;		
	
		if(user::validateAdmin()){
			$admin = '';
			$password = '';
		} else {
			$admin = 'admin';
			$password = '1234';
		}	
	
		$body = form::beginForm('install',PATH_WEB.'/common/install/systeminstall');
			$body .= form::fieldset('field1','<h3>Administrator</h3>',form::input($admin,'user',TEXT));
			$body .= form::fieldset('field2','<h3>Password</h3>',form::input($password,'password',TEXT));	
			$body .= form::fieldset('field3','<h3>Frontpage</h3>',form::input($FRONTPAGE,'FRONTPAGE',TEXT));		
			$body .= form::fieldset('field4','<h3>Web path</h3>',form::input($PATH_WEB,'PATH_WEB',TEXT));
			$body .= form::fieldset('field5','<h3>Database</h3>',form::input($DATABASE,'DATABASE',TEXT));
			$body .= form::fieldset('field6','<h3>Database user</h3>',form::input($DBUSER,'DBUSER',TEXT));
			$body .= form::fieldset('field7','<h3>Database password</h3>',form::input($DBPASSWORD,'DBPASSWORD',TEXT));
			$body .= form::fieldset('field8','<h3>Database server</h3>',form::input($DATABASESERVER,'DATABASESERVER',TEXT));
			$body .= form::fieldset('field9','<h3>Environment</h3>',form::input($ENVIRONMENT,'ENVIRONMENT',TEXT));
			$body .= form::fieldset('field10','<h3>Table prepend</h3>',form::input($TPREP,'TPREP',TEXT));
			$body .= form::fieldset('field11','<h3>Standard language</h3>',form::input($STD_LANGUAGE,'STD_LANGUAGE',TEXT));
			$body .= form::fieldset('field12','<h3>Error report</h3>',form::input($ERROR_REPORT,'ERROR_REPORT',TEXT));
			$body .= form::fieldset('field13','<h3>Log error</h3>',form::input($LOG_ERROR,'LOG_ERROR',TEXT));			
			$body .= form::fieldset('field14','<h3>Setup database</h3>',form::check(0,'setDB')).'<br />';
		$body .= form::endForm('install');
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
			template::title('Banana Peel install');
			template::replace('[COPY]','Banana Peel install');		
		template::end();	
	}
	
	public static function systeminstallAction($args){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
				route::error(403);
			}
		}		
		if(form::validate('install')){
			self::$rootuser = ($args['user']) ? $args['user'] : '';
			self::$rootpassword = ($args['password']) ? $args['password'] : '';

			$user_defines = 'settings/user_defines.php';
			$current = '<?php
			/**
			 * This file contains all the user defined values used in the portal.
			 */'.chr(13);
			$current .= 'define("PATH_WEB","'.$args['PATH_WEB'].'");'.chr(13);
			$current .= 'define("FRONTPAGE","'.$args['FRONTPAGE'].'");'.chr(13);
			$current .= 'define("DATABASE","'.$args['DATABASE'].'");'.chr(13);
			$current .= 'define("DBUSER","'.$args['DBUSER'].'");'.chr(13);
			$current .= 'define("DBPASSWORD","'.$args['DBPASSWORD'].'");'.chr(13);
			$current .= 'define("DATABASESERVER","'.$args['DATABASESERVER'].'");'.chr(13);
			$current .= 'define("ENVIRONMENT","'.$args['ENVIRONMENT'].'");'.chr(13);
			$current .= 'define("ERROR_REPORT","'.$args['ERROR_REPORT'].'");'.chr(13);
			$current .= 'define("LOG_ERROR","'.$args['LOG_ERROR'].'");'.chr(13);	
			$current .= '/**
			 * If for some reason, it is necessary to prepend table names
			 * Since the database will be installed using this variable, you need to take care
			 * when changing this variable.
			 */';
			$current .= 'define("TPREP","'.$args['TPREP'].'");'.chr(13);
			$current .= 'define("STD_LANGUAGE","'.$args['STD_LANGUAGE'].'");'.chr(13);
			$current .= '?>';			
			file_put_contents($user_defines, $current);
			if(isset($args['setDB'])){
				self::installDatabase($args['PATH_ROOT']);
			}
		}
		if(!user::validateAdmin()){
			route::redirect('system/login/');
		} else {
			route::redirect('common/install/list');
		}		
	}
	
	public static function installDatabase($dir){
		$folders = scandir($dir);
		foreach($folders as $folder){
			if(	$folder != '.' && 
				$folder != '..' && 
				$folder != 'contributions' &&
				$folder != 'uploads' &&
				$folder != 'template' &&
				$folder != 'settings' &&
				$folder != 'scripts' &&
				$folder != 'plugins' &&
				$folder != 'cache' &&
				$folder != 'audio' &&
				$folder != 'common' ){
				if(is_dir($dir.'/'.$folder)) {
					if(strpos($folder, 'control')){
						include_once($dir.'/'.$folder.'/'.$folder.'.php');
						if(is_callable(array($folder,'installAction'))){
							$folder::installAction(self::$rootuser,self::$rootpassword);
							echo 'installed: '.$folder.'::installAction<br />';
						}
					}
					self::installDatabase($dir.'/'.$folder);
				}
			}
		}
	}
}
?>