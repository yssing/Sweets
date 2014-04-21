<?php
	/**
	 * This file contains all the standard defined values used in the portal.
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
	 * @category	Generic system methods
	 * @package		defines
	 * @author		Frederik Yssing <yssing@yssing.org>
	 * @copyright	2012-2014 Yssing
	 * @version		SVN: 1.0.0
	 * @link		http://www.yssing.org
	 * @since		File available since Release 1.0.0
	 */
	 	
	include_once('user_defines.php');
	include_once('key_defines.php');
	
	/**
     * Defines for the various paths used in the portal
     */
	define("PATH_SCRIPT", 	PATH_WEB."/");
	define("PATH_SYS", 		PATH_SCRIPT . "system/");
	define("PATH_CLASS", 	PATH_SCRIPT . "system/class/");
	define("PATH_CONTRIB", 	PATH_SCRIPT . "contributions/");
	define("HIDDEN",2);
	define("PASSWORD",1);
	define("TEXT",0);
	
	/**
     * How long is a cookie active
     */	
	define("DEF_CLEANTIME",3);
	
	date_default_timezone_set('Europe/Copenhagen');
?>