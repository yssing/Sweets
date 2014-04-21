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
	autoload::load();

	// now we find various keys and use them
	// template setup
	$template = (key::readValue('[TEMPLATE]')) ? key::readValue('[TEMPLATE]') : "default";
	if(is_dir('template/'.$template)){	
		define("TEMPLATE",$template);
	} else {
		define("TEMPLATE","default");
	}
	
	$userfooter = (text::readTextByKey('[USERFOOTER]')) ? text::readTextByKey('[USERFOOTER]') : '';
	define("USERFOOTER",$userfooter);	
	$copyfooter = (text::readTextByKey('[COPYFOOTER]')) ? text::readTextByKey('[COPYFOOTER]') : '';
	define("COPYFOOTER",$copyfooter);
	$userheader = (text::readTextByKey('[USERHEADER]')) ? text::readTextByKey('[USERHEADER]') : '';
	define("USERHEADER",$userheader);
	$title = (key::readValue('[SITENAME]')) ? key::readValue('[SITENAME]') : '';
	define("TITLE",$title);
	$sitemail = (key::readValue('[SITEMAIL]')) ? key::readValue('[SITEMAIL]') : '';
	define("SITEMAIL", $sitemail);
	$paging = (key::readValue('[PAGING]')) ? key::readValue('[PAGING]') : '25';
	define("PAGING", $paging);	
	
	// author and meta tag
	$author = (key::readValue('[AUTHOR]')) ? key::readValue('[AUTHOR]') : '';
	define("AUTHOR",$author);
	$meta = (key::readValue('[META]')) ? key::readValue('[META]') : '';
	define("META",$meta);
?>