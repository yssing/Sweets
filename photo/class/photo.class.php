<?php
/**
 * This class handles photo administration.
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
 * @category   	Photo methods
 * @package		photo
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 */
 
class photo{
	/**
     * This method creates a photo entry in the database.
	 *
	 * @param string $Name Photo name.
	 * @param string $Description Photo description.
	 * @param string $URL Photo URL.
	 * @param string $language The language of the text.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */	
	public static function createPhoto($Name,$Description,$URL,$language = STD_LANGUAGE){
		$database = new database();
		$data = array("Name" => "'".$Name."'",
			"Description" => "'".$Description."'",
			"URL" => "'".$URL."'",
			"Language" => "'".$language."'");			
		if(!$database->create('photo',$data)){
			return false;
		}	
	}
}
?>