<?php
/**
 *
 * This class handles all actions in the gallery plugin.
 *
 * It is used both by user and administrator.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt. If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category	Gallery
 * @package		plugins
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @license		http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since 2013-12-22
 */

class gallery /*extends database*/{
	
	public static function indexAction(){	
		if(!user::validateAdmin()){
			route::error(403);
		}
		$database = new database();

		$body = views::displayEditListview($database->read('gallery','PK_GalleryID, Gallery, Image, Width, Height'));
			$body .= form::beginForm('gallery','/plugins/gallery/insert');
			$body .= form::fieldset('fld2','Galleri navn',form::input('','galleryName',0));
			$body .= form::fieldset('fld2','Billede',form::select(files::listFolderContent('uploads/medium'),'---','galleryImage',1).'<br /><br />');
		$body .= form::endForm('gallery');
		//$body .= '<br /><a href="createtable">Create table</a><br />';
		
		template::initiate('admin');
			template::noCache();
			template::title('Plug-ins');
			template::header('<h3>Rediger galleri</h3>');
			template::replace('[MENU]',adminmenu::createMenu($_SERVER['DOCUMENT_ROOT'].'/'));
			template::body($body);	
			template::replace('[PATH]',PATH_WEB);
		template::end();		
	}
	
	public static function installAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}
		$database = new database();
		$what = array("Image" => "varchar(100)", "Gallery" => "varchar(100)", "Width" => "int(10)", "Height" => "int(10)");
		$result = $database->createTable('gallery',$what,"PK_GalleryId");
		route::redirect('/plugins/gallery');	
	}
	
	public static function insertAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		$database = new database();
		list($Width,$Height) = getimagesize('uploads/images/'.$args['galleryImage']);
		$values = array("Image" =>  "'".$args['galleryImage']."'", "Gallery" => "'".$args['galleryName']."'", "Width" => $Width, "Height" => $Height);
		if(!$database->create('gallery',$values)){
			$database->TransactionRollback();
		}
		$database->TransactionEnd();
		route::redirect('/plugins/gallery');
	}
	
	public static function editAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}
		$database = new database();
		route::redirect('/plugins/gallery');
	}
	
	public static function listAction($args){
		$database = new database();
		echo views::displayJSON($database->read('gallery','Image, Width, Height','Gallery = \''.$args[0].'\''));
	}
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		$database = new database();
		$database->destroy('gallery','PK_GalleryId = '.$args[0]);
		route::redirect('/plugins/gallery');
	}
}
?>