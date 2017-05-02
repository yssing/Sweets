<?php
class menu{
	/**
	 * Create a new menu.
	 *
	 * @param string $menuname The name of the menu, used for admin only
	 * @param string $menukey The key to the menu.
	 *
	 * @return array/bool The new id on succes false on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0	 
	 */
	public static function createMenu($menuname,$menukey){
		$dbobject = new dbobject('cms_menu');
		$dbobject->create('MenuName',$menuname);
		$dbobject->create('MenuKey',$menukey);
		$dbobject->create('Language',language::get());
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		} 
		return false;
	}
	
	public static function updateMenu($menuId, $menuname, $menukey){		
		$dbobject = new dbobject('cms_menu');
		$dbobject->update('MenuName',$menuname);
		$dbobject->update('MenuKey',$menukey);
		$dbobject->update('Language',language::get());
		$dbobject->where("PK_MenuID",$menuId);
		return $dbobject->commit();
	}	
	
	/**
	 * Wrapper for submenu::makeMenu()
	 */
	public static function makeMenu($menukey = 'MENU'){
		return submenu::makeMenu($menukey);
	}

	/**
     * This method reads and returns the menu table.
	 *
	 * @return array/bool The table on succes false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */
	public static function listMenu(){
		$dbobject = new dbobject('cms_menu');
		$dbobject->read("PK_MenuID");
		$dbobject->read("MenuName");
		$dbobject->read("MenuKey");
		$dbobject->where("Language",language::get());
		$dbobject->orderby("MenuKey");
		return $dbobject->fetch();
	}
	
	/**
	 * reads a single menu
	 */
	public static function readMenu($menuId){
		$dbobject = new dbobject('cms_menu');
		$dbobject->read("PK_MenuID");
		$dbobject->read("MenuName");
		$dbobject->read("MenuKey");
		$dbobject->where("PK_MenuID",$menuId);
		$dbobject->orderby("MenuKey");
		return $dbobject->fetchSingle();
	}	
	
	/**
     * This method deletes a menu entry in the database.
	 *
	 * @param int $menuId The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyMenu($menuId){
		$dbobject = new dbobject('cms_menu');
		$dbobject->destroy();
		$dbobject->where("PK_MenuID",$menuId);
		return $dbobject->commit();		
	}
}
?>