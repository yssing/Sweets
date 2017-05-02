<?php
/**
 * This class handles different methods for importing data to the database
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
 * @category   	Import methods
 * @package    	database class
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 * @require		'database.class.php'
 */
//require_once('system/class/dbobject.class.php'); 
//class import extends dbobject{
class import{

	/**
	 * This method imports a CSV file and will then put the data in to the table given.
	 * It will fail if no table name is given.
	 * Inserting to the table will fail, if the first line DOES NOT contain
	 * the name of the fields.
	 *
	 *
	 * @param string $csvfile The CSV file to import.
	 * @param string $table The table to insert in to.
	 * @param string $delimiter Single chars, used to separate the columns.
	 *
	 * @return true on success or false on failure.
	 *
     * @access public
	 */
	public static function importCSV($csvfile,$table = '',$delimiter = ","){
		if (!file_exists($csvfile)) {
			return false;
		}
		$headline = array();
		if(!$table){
			return false;
		}
		if (($handle = fopen($csvfile, "r")) !== false) {
			$nn = 0;
			while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {				
				if($nn > 0){
					$dbobject = new dbobject($table);
				}
				$c = count($data);
				for ($x=0;$x<$c;$x++){
					if($nn == 0){
						$headline[$x] = $data[$x];
					} else {
						// create the insertion
						$dbobject->create($headline[$x],$data[$x]);
					}
				}
				if($nn > 0){
					$dbobject->commit();
				}
				$nn++;
			}
			fclose($handle);
		}
		return true;
	}
	
	public static function importJSON($jsonfile,$table){
		if (!file_exists($jsonfile)) {
			return false;
		}
		$jsonarray = array();
		
		$string = file_get_contents($jsonfile);
		$jsonarray = json_decode($string,true);		
		var_dump($jsonarray);

		return $jsonarray;		
	}
	
	public static function importXML($xmlfile,$table){	
		if (!file_exists($xmlfile)) {
			return false;
		}
		$xmlarray = array();
		
		$xmlstr = file_get_contents($xmlfile);
		$xmlcont = new SimpleXMLElement($xmlstr);		
		var_dump($xmlcont);

		return $xmlcont;			
	}	
}
?>