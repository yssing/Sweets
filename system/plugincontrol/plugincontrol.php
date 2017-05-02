<?php
class plugincontrol{
	public static function indexAction(){
		route::error(403);
	}
	
	public static function listAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}
	
		$body = views::displayEditListview(plugin::listPlugin(),'listview',0);
		$body .= '<br />';
		$body .= form::beginForm('upload','system/plugin/upload');		
			$body .= form::file('','file');
			$body .= form::submit('Upload','submit',1,array("style" => "margin-left:5px;"));
		$body .= form::endForm('upload',false);			
		$body .= views::displayListview(plugin::listerPlugin(6),'');
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();		
	}
	
	/**
	 * This method will take the plugin file and unzip it if possible.
	 * If unpacking is successful, it will then try to run an installAction, 
	 * but only if an installAction can be found within the new plugin class.
	 */
	public static function uploadAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}
		if (form::validate('upload')){
			$filename = $_FILES["file"]["name"];
			if (form::getFileExtension($_FILES["file"]["name"])){
				if ($_FILES["file"]["error"] == 0){
					move_uploaded_file($_FILES["file"]["tmp_name"],'plugins/'.$filename);
					$zip = new ZipArchive;
					if ($zip->open('plugins/'.$filename) === true) {
						$zip->extractTo('plugins/');
						$zip->close();
						list($pluginname) = explode('.',$filename); 
						
						self::installTable($pluginname);
					}
					unlink('plugins/'.$filename);
				}
			}			
		}
		route::redirect('system/plugin/list');
	}
	
	private static function installTable($plugin){
		if (file_exists ('plugins/'.$plugin.'/'.$plugin.'.php')){
			require_once('plugins/'.$plugin.'/'.$plugin.'.php');
			if (method_exists($plugin,'installAction')){
				$plugin::installAction();
			}
		}
	}
	
	public static function toggleAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$pluginid = plugin::doesExist($args[0]);
		if ($pluginid){
			list($pluginid,$active) = plugin::readPlugin($pluginid);
			if ($active){
				plugin::disablePlugin($pluginid);
			} else {
				self::installTable($args[0]);
				plugin::enablePlugin($pluginid);
			}
		} else {
			self::installTable($args[0]);
			plugin::addPlugin($args[0]);
		}
		route::redirect('system/plugin/list');		
	}
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		list($pluginid,$active,$name) = plugin::readPlugin($args[0]);		
		if (is_file('plugins/'.$name.'/'.$name.'.php')){
			route::redirect('/plugins/'.$name);
		} else {			
			list($pluginid,$active) = plugin::readPlugin($pluginid);
			if ($active){
				plugin::disablePlugin($pluginid);
			} else {
				plugin::enablePlugin($pluginid);
			}
			route::redirect('system/plugin/list');
		}		
	}
	
	public static function installAction(){
		/*if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}*/
		$databaseadmin = new databaseadmin();
		$what = array("Name" => "varchar(200)", 
					  "Activated" => "tinyint(1)");
		$result = $databaseadmin->createTable('generic_plugin',$what,"PK_PluginID");
	}	
	
	public static function removeAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		form::rrmdir('plugins/'.$args[0]);
		plugin::destroyPlugin($args[0]);
		route::redirect('system/plugin/list');	
	}
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		plugin::destroyPlugin($args[0]);
		route::redirect('system/plugin/list');
	}
}
?>