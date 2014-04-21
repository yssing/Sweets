<?php
/**
 * This class takes the ip as a parameter and find the 
 * approximated location of the user.
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
 * @package    	geo location
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 * @require		'generic.IO.class.php'
 */
require_once('generic.IO.class.php');
class netGeo extends genericIO{

	/**
     * The ip adress to look up, if supplied.
	 * freegeoip will find the best ip if no Ip is given.
	 *
     * @var string Ip
     * @access public
	 * @static
     */	
	public static $Ip = '0';
	/**
     * Country code in letters eg. DK for Denmark.
	 *
     * @var string CountryCode
     * @access public
	 * @static
     */		
	public static $CountryCode = '';
	/**
     * @var string CountryName
     * @access public
	 * @static
     */		
	public static $CountryName = '';
	/**
     * @var string RegionCode
     * @access public
	 * @static
     */		
	public static $RegionCode = '';
	/**
     * @var string RegionName
     * @access public
	 * @static
     */		
	public static $RegionName = '';
	/**
     * @var string City
     * @access public
	 * @static
     */		
	public static $City = '';
	/**
     * @var string ZipCode
     * @access public
	 * @static
     */		
	public static $ZipCode = '';
	/**
     * @var string Latitude
     * @access public
	 * @static
     */		
	public static $Latitude = '';
	/**
     * @var string Longitude
     * @access public
	 * @static
     */		
	public static $Longitude = '';
	/**
     * @var string MetroCode
     * @access public
	 * @static
     */		
	public static $MetroCode = '';
	/**
     * @var string AreaCode
     * @access public
	 * @static
     */		
	public static $AreaCode = '';
	
	/**
	 * This method is used to ask freegeoip for geo information based on
	 * either the supplied IP or the IP of the asker.
	 * The answer from freegeoip is an XML file. 
	 *
	 * @param string $ip The Ip address to ask on.   
	 *
	 * @return bool Returns TRUE on success or FALSE on failure.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function getNetGeo($ip = 0){		
		if($ip){
			$url = 'http://freegeoip.net/xml/'.$ip;
		} else {
			$url = 'http://freegeoip.net/xml/';
		}

		try{
			$result = file_get_contents($url, false);
			if(!$result){
				throw new Exception('Could not get a response from freegeoip.net');
				return false;
			} else {
				self::pickDataFromXML($result);
			}
		}
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(). ' in method: '.__METHOD__.' in file: '.__FILE__);
			return false;
		}	
		return true;
	}	
	
	/**
	 * This method finds the relevant data from the returned xml file.
	 * It then assigns those data to the variables.
	 *
	 * @param string $file The xml file with the data found..   
	 *
	 * @return bool Returns TRUE on success or FALSE on failure.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 * @todo use a pointer to the xml file.
	 */			
	public static function pickDataFromXML($file){
		$XMLFile = '';
		try{
			$XMLFile = simplexml_load_string($file);
			if(!$XMLFile){
				self::DBug('Could not load XML file!');
				return false;
			} else {
				self::$Ip = (string)$XMLFile->Ip;
				self::$CountryCode = (string)$XMLFile->CountryCode;
				self::$CountryName = (string)$XMLFile->CountryName;
				self::$RegionCode = (string)$XMLFile->RegionCode;
				self::$RegionName = (string)$XMLFile->RegionName;
				self::$City = (string)$XMLFile->City;
				self::$ZipCode = (string)$XMLFile->ZipCode;
				self::$Latitude = (string)$XMLFile->Latitude;
				self::$Longitude = (string)$XMLFile->Longitude;
				self::$MetroCode = (string)$XMLFile->MetroCode;
				self::$AreaCode = (string)$XMLFile->AreaCode;
				//unlink($XMLFile);	
			}
		}
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(). ' in method: '.__METHOD__.' in file: '.__FILE__);
			return false;
		}
		return true;
	}	
}
?>