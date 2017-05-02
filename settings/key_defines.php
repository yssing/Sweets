<?php
	/**
	 * This file loads all the defines found in the key table.
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
	
	require_once('system/class/load.class.php');
	autoload::load('system/class/','modules/');

	// now we find various keys and use them
	// template setup

	if ($value = key::readValue('TEMPLATE')){
		if (is_dir('template/'.$value)){	
			define("TEMPLATE",$value);
		} else {
			define("TEMPLATE","default");
		}
	} else {
		define("TEMPLATE","default");
	}

	if ($value = text::readTextByKey('USERFOOTER')){
		define("USERFOOTER",$value);
	} else {
		define("USERFOOTER","");
	}
	
	if ($value = text::readTextByKey('COPYFOOTER')){
		define("COPYFOOTER",$value);
	} else {
		define("COPYFOOTER","");
	}
	
	if ($value = text::readTextByKey('USERHEADER')){
		define("USERHEADER",$value);
	} else {
		define("USERHEADER","");
	}

	if ($value = key::readValue('SITENAME')){
		define("TITLE",$value);
	} else {
		define("TITLE","default");
	}
	
	if ($value = key::readValue('SITEMAIL')){
		define("SITEMAIL",$value);
	} else {
		define("SITEMAIL","");
	}
	
	if ($value = key::readValue('PAGING')){
		define("PAGING",$value);
	} else {
		define("PAGING","25");
	}

	// author and meta tag
	if ($value = key::readValue('AUTHOR')){
		define("AUTHOR",$value);
	} else {
		define("AUTHOR","");
	}
	
	if ($value = key::readValue('USE_HTTPS')){
		define("USE_HTTPS",intval($value));
	} else {
		define("USE_HTTPS",0);
	}
	
	if ($value = key::readValue('META')){
		define("META",$value);
	} else {
		define("META","");
	}
?>