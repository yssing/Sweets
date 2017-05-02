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

class gallery{
	
	public static function indexAction(){	
		if (!user::validateAdmin()){
			route::error(403);
		}

		$dbobject = new dbobject('gallery');
		$dbobject->read("PK_GalleryID");
		$dbobject->read("Gallery");
		$dbobject->read("Image");
		$dbobject->read("Width");
		$dbobject->read("Height");

		$body = views::displayEditListview($dbobject->fetch());
			$body .= '<br><br><br>';
			$body .= form::beginForm('gallery','/plugins/gallery/insert');
			$body .= form::fieldset('fld2','Galleri navn',form::input('','galleryName',0));
			
			if(modules::isModule('ephoto')){
				$body .= photo::showControl();
			} else {
				$body .= form::fieldset('fld2','Billede',form::select(files::listFolderContent('uploads/images/medium'),'---','icon',1).'<br /><br />');
			}
		$body .= form::endForm('gallery');

		template::initiate('admin');
			template::title('Plug-ins');
			template::header('<h3>Rediger galleri</h3>');
			template::body($body);	
			template::replace('[PATH]',PATH_WEB);
		template::end();
	}
	
	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Image" => "varchar(255)",
			"Gallery" => "varchar(100)",
			"Width" => "int(10)",
			"Height" => "int(10)");
		$result = $databaseadmin->createTable('gallery',$what,"PK_GalleryId");
		route::redirect('/plugins/gallery');
	}	
	
	public static function insertAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		if(modules::isModule('ephoto')){
			list($Width,$Height) = getimagesize($args['icon']);
			$img = $args['icon'];
		} else {
			list($Width,$Height) = getimagesize('uploads/images/full/'.$args['icon']);
			$img = 'uploads/images/full/'.$args['icon'];
		}

		$dbobject = new dbobject('gallery');
		$dbobject->create('Image',$img);
		$dbobject->create('Gallery',$args['galleryName']);
		$dbobject->create('Width',$Width);
		$dbobject->create('Height',$Height);
		$dbobject->commit();
		
		route::redirect('/plugins/gallery');
	}
	
	public static function editAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}
		route::redirect('/plugins/gallery');
	}
	
	public static function listAction($args){
		$dbobject = new dbobject('gallery');

		$dbobject->read("Image");
		$dbobject->read("Width");
		$dbobject->read("Height");		
		$dbobject->where("Gallery",$args[0]);		
		
		echo views::displayJSON($dbobject->fetch());
	}
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		$dbobject = new dbobject('gallery');
		$dbobject->destroy();
		$dbobject->where("PK_GalleryId",$args[0]);		
		$dbobject->commit();
		route::redirect('/plugins/gallery');
	}
}
?>