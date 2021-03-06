<?php
/**
 * This class is used to handle module verification
 *
 * Copyright (C) <2014> <Frederik Yssing>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   	Generic system methods
 * @package    	modules
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2015 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 25-10-2015
 */
class modules{
	
	/**
	 * Makes a simple check, to see if a module folder exists or not
	 *
	 * @param string $module The modules to check.
	 *
	 * @return bool true if found or false if not found.	 
	 *
	 * @access public
	 * @since Method available since Release 25-10-2015
	 */
	public static function isModule($module){
		if(is_dir('modules/'.$module)){
			return true;
		} else {
			return false;
		}
	}

}

?>