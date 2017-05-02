<?php
	error_reporting(E_ALL);

	// this script, and this alone does not follow Sweets standard, this is done in order to be able to set up the database if it has not been setup yet.
	$error_msg = '';

	if (isset($_REQUEST['install'])){
		$user_defines = $_SERVER['DOCUMENT_ROOT'].'/settings/user_defines.php';
		if (!file_exists($user_defines)){
			$MEMCACHE_RUNNING = ($_REQUEST['MEMCACHE_RUNNING']) ? $_REQUEST['MEMCACHE_RUNNING'] : 0;
			$current = '<?php
			/**
			 * This file contains all the user defined values used in the portal.
			 */'.chr(13);
			$current .= 'define("PATH_WEB","'.$_REQUEST['PATH_WEB'].'");'.chr(13);
			$current .= 'define("FRONTPAGE","'.$_REQUEST['FRONTPAGE'].'");'.chr(13);
			$current .= 'define("DATABASE","'.$_REQUEST['DATABASE'].'");'.chr(13);
			$current .= 'define("DBUSER","'.$_REQUEST['DBUSER'].'");'.chr(13);
			$current .= 'define("DBPASSWORD","'.$_REQUEST['DBPASSWORD'].'");'.chr(13);
			$current .= 'define("DATABASESERVER","'.$_REQUEST['DATABASESERVER'].'");'.chr(13);
			$current .= 'define("ENVIRONMENT","'.$_REQUEST['ENVIRONMENT'].'");'.chr(13);
			$current .= 'define("ERROR_REPORT",'.$_REQUEST['ERROR_REPORT'].');'.chr(13);
			$current .= 'define("LOG_ERROR","'.$_REQUEST['LOG_ERROR'].'");'.chr(13);
			$current .= '/**
			 * If for some reason, it is necessary to prepend table names
			 * Since the database will be installed using this variable, you need to take care
			 * when changing this variable.
			 */'.chr(13);
			$current .= 'define("TPREP","'.$_REQUEST['TPREP'].'");'.chr(13);
			$current .= 'define("STD_LANGUAGE","'.$_REQUEST['STD_LANGUAGE'].'");'.chr(13);
			$current .= 'define("MEMCACHE_RUNNING","'.$MEMCACHE_RUNNING.'");'.chr(13);
			$current .= 'define("DATABASETYPE","'.$_REQUEST['DATABASETYPE'].'");'.chr(13);
			$current .= '?>';
			//file_put_contents($user_defines, $current);
			file_put_contents($user_defines, str_replace("\t", "", $current));

			// creating new database if one is not found
			$pdo = new PDO("mysql:host=".$_REQUEST['DATABASESERVER'], $_REQUEST['DBUSER'], $_REQUEST['DBPASSWORD']);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$dbname = "`".str_replace("`","``",$_REQUEST['DATABASE'])."`";
			if ($_REQUEST['USEEXISTINGDB'] == 1){
				$pdo->query("CREATE DATABASE IF NOT EXISTS $dbname");
				$pdo->query("use $dbname");	
			}

			define("PATH_ROOT", 	$_SERVER['DOCUMENT_ROOT']."/");
			define("PATH_MOD",		PATH_ROOT . "modules/");
			define("PATH_SYS", 		PATH_ROOT . "system/");	

			require_once($user_defines);
			require_once('../../system/class/load.class.php');
			autoload::load('../../system/class/','../../modules');
			//autoload::load('../../system/class/');
		
			/**
			 * Installing system tables by remoting to the install actions.
			 */
			route::remote('../../system/settings','install');
			route::remote('../../system/plugin','install');
			route::remote('../../system/salt','install');
			route::remote('../../system/userstatus','install');
			route::remote('../../system/user','install');

			/**
			 * Tables installed, then create the admin user.
			 */
			if ($id = user::createUser($_REQUEST['user'],'ADMIN')){
				if (user::updateUserPassword($_REQUEST['password'],$id,'ADMIN_SECRET')){
					header('location: '.$_REQUEST['PATH_WEB'].'/admin');
				}
			}	

			/**
			 * Install module tables after the system tables.
			 */
			//if (is_dir('../../modules')) {
				/*
				$folders = scandir('../../modules');
				foreach($folders as $folder){
					if ($folder != '.' && $folder != '..'){
						if (is_dir('../../modules/'.$folder)) {
							$control_folders = scandir('../../modules/'.$folder);
							foreach($control_folders as $control_folder){
								if ($control_folder != '.' && $control_folder != '..'){
									if (strpos($control_folder, 'control')){
										include_once('../../modules/'.$folder.'/'.$control_folder.'/'.$control_folder.'.php');
										if (is_callable(array($control_folder,'installAction'))){
											//echo $control_folder . ' installAction is callable<br>';
											$control_folder::installAction();
										}
									}
								}
							}
						}
					}
				}
				*/
			//}	
			header('location: '.$_REQUEST['PATH_WEB'].'/admin');
			
			$error_msg = 'settings file created!';
		} else {
			$error_msg = 'settings exists so this page can not be used to change it!!';
		}
	}
?>

<!DOCTYPE html>
<html lang="da">
	<head>
		<meta charset="utf-8">
		<meta name="author" content="Frederik Yssing" />
		<meta name="generator" content="notepad++" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Sweets Install</title>
		<!-- scripts -->
		<link href="/contributions/jquery-ui/css/redmond/jquery-ui-1.10.4.custom.css" rel="stylesheet">
		<script src="/contributions/jquery-ui/js/jquery-1.10.2.js"></script>
		<script src="/contributions/jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>

		<!-- bootstrap -->
		<link href="/contributions/bootstrap-3.1.1/dist/css/bootstrap.css" rel="stylesheet">
		<link href="/contributions/bootstrap-3.1.1/dist/css/bootstrap-responsive.css" rel="stylesheet">
		<script src="/contributions/bootstrap-3.1.1/dist/js/bootstrap.min.js" type="text/javascript"></script>

		<!-- Template -->
		<link rel="stylesheet/less" type="text/css" href="/template/default/style/frontpage.less" />
		<script src="/contributions/less.min.js" type="text/javascript"></script>	
		<script src="/scripts/js-collection.js" type="text/javascript"></script>
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script type="text/javascript">
			$(document).ready(function() {
				$('[data-toggle=tooltip]').tooltip();
			}); 
		</script>
	</head>
	<body>
		<div class="container holder">
			<div class="text-header">Sweets Install</div>
			<div class="scrolltarget"></div>
			<div class="content">
				<h3><?php echo $error_msg;?></h3>
				This page is used to install the database with the correct tables and setting up user defines.<br /><br />
				<i>If you already have a database set up, just fill in the fields below, in case you have not, just fill in the fields below with
				the settings you want to use.</i><br />
				<b>You can not use this page to overwrite an existing database and its data!</b>
				<br /><br />
				<form action="/common/install/first.php" method="post">
					<div class="col-lg-6">
						<h4>Administrator</h4><input type="text" name="user" id="user" placeholder="default: admin" class="form-control" />
						<h4>Password</h4><input type="text" name="password" id="password" placeholder="default: 1234" class="form-control" />
					</div>
					<div class="col-lg-6">
						<h4>Frontpage</h4><input type="text" name="FRONTPAGE" id="FRONTPAGE" placeholder="the path to the frontpage action" class="form-control" />
						<h4>Web path</h4><input type="text" name="PATH_WEB" id="PATH_WEB" placeholder="the full url to the site" class="form-control" />
					</div>
					<div class="col-lg-6">
						<h4>Database</h4><input type="text" name="DATABASE" id="DATABASE" placeholder="the name of the database" class="form-control" />
						<h4>Database user</h4><input type="text" name="DBUSER" id="DBUSER" placeholder="the name of the database user" class="form-control" />
						<h4>Database password</h4><input type="text" name="DBPASSWORD" id="DBPASSWORD" placeholder="the password" class="form-control" />
						<h4>Database server</h4><input type="text" name="DATABASESERVER" id="DATABASESERVER" placeholder="the database server" class="form-control" />
						<h4>Table prepend</h4><input type="text" name="TPREP" id="TPREP" placeholder="if prepending tables with a name" class="form-control" />
						<h4>Database type</h4>
						<select name="DATABASETYPE" id="DATABASETYPE" class="form-control">
							<option value="MYSQL">MySQL</option>
							<option value="SQLITE">SQLITE</option> 
						</select>
						<h4>Use existing database</h4>
						<select name="USEEXISTINGDB" id="USEEXISTINGDB" class="form-control">
							<option value="0">Yes, use existing</option>
							<option value="1">No, create new database</option>
						</select>
					</div>
					<div class="col-lg-6">
						<h4>Environment</h4>
						<select name="ENVIRONMENT" id="ENVIRONMENT" class="form-control">
							<option value="TEST">TEST</option>
							<option value="LIVE">LIVE</option>
						</select>
						<h4>Standard language</h4><input type="text" name="STD_LANGUAGE" id="STD_LANGUAGE" placeholder="the main language eg: GB, DE or DK" class="form-control" />
						<h4>Error report</h4>
						<select name="ERROR_REPORT" id="ERROR_REPORT" class="form-control">
							<option value="0">None</option>
							<option value="E_ERROR">E_ERROR</option>
							<option value="E_WARNING">E_WARNING</option>
							<option value="E_PARSE">E_PARSE</option>
							<option value="E_NOTICE">E_NOTICE</option>
							<option value="E_STRICT">E_STRICT</option>
							<option value="E_ALL">E_ALL</option>
						</select>
						<h4>Log error</h4>
						<select name="LOG_ERROR" id="LOG_ERROR" class="form-control">
							<option value="0">No, don't log errors</option>
							<option value="1">Yes, please log errors</option>
						</select>
						<h4>Memcache</h4>
						<select name="MEMCACHE_RUNNING" id="MEMCACHE_RUNNING" class="form-control">
							<option value="0">No, memcache is not installed</option>
							<option value="1">Yes, use memcache</option>
						</select>
					</div>
					<div class="col-lg-12">	
						<input type="submit" name="install" id="install" value="install" /></form>
					</div>
				</form>
			</div>	
		</div>			
		<div class="footer1 hidden-xs">
			<div class="container footer1block">
				<div class="container footer1content"></div>
				<div class="container footer1text">Sweets Install</div>
			</div>
		</div>
	</body>
</html>