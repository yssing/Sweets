<?php
	/**
	 * The index file takes care of setting up the system
	 * Only mess with it if you know what you are doing!
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
	 
	// loading classes
	require_once('system/class/load.class.php');
	autoload::load();

	// lets determine the language
	if(!isset($_SESSION['CountryCode'])){
		$_SESSION['CountryCode'] = (netGeo::getNetGeo()) ? netGeo::$CountryCode : STD_LANGUAGE;
	}	

	// lets get any arguments parsed
	$args = route::getArgs();

	if(isset($args['clearcache'])){
		template::clearCache();
	}
	
	if(isset($args['lan']) && $args['lan']){
		userlanguage::setLanguage($args['lan']);
		$_SESSION['LanguageVars'] = '';
		route::redirect(route::getBaseURL());
	}

	// routing! and caching
	if($path = route::isCached()){
		require_once($path);
	} else {
		template::setTheme(TEMPLATE);
		if(!user::countUser('ADMIN')){
			//if no admin user is found, we need to install banana peel first.
			route::redirect('common/install');
		}
		route::autoRoute($_SERVER['REQUEST_URI']);
	}
?>