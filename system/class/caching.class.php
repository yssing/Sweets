<?php
/**
 * This class handles all the caching.
 * It uses memcache if available, if not, it will cache data using SESSION
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
 * @package    	Caching
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 */

class caching{
	/**
     * Holds the value of memcache status. 
	 *
     * @var string MEMCACHE_RUNNING
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
     */		
	public static $RUNNING = MEMCACHE_RUNNING;
	
	public static $SERVER = 'localhost';

	public static $SAVE_TIME = 300;
	
	/**
	 * Checks if memcache is running
	 * If so, then sets RUNNING to true
	 *
     * @access protected
	 * @since Method available since Release 1.0.0
	 */	
	public static function isCacheRunning(){
		if (class_exists('Memcache')){
			$memcache = new Memcache();
			$isMemcacheAvailable = @$memcache->connect(self::$SERVER);

			if ($isMemcacheAvailable){
				$aData = $memcache->get('data');
				if (!$aData){
					$aData = array(
						'me' => 'you',
						'us' => 'them',
					);
					$memcache->set('data', $aData, 0, self::$SAVE_TIME);
				}
				$aData = $memcache->get('data');
				if ($aData) {
					return 1;
				} 
			}
		}
		return 0;
	}
	
	/**
     * Set a key
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return bool Returns true on success false on failure
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function setKey($key,$value){
		if (self::$RUNNING === true){
			$memcache = new Memcache();
			@$memcache->connect(self::$SERVER);			
			$keyData = $memcache->get($key);
			return $memcache->set($key, $value, false, self::$SAVE_TIME);			
		} else {
			//return $_SESSION[$key] = $value;
		}
		return false;
	}

	/**
     * Return the value of a key, if the key is set.
	 *
	 * @param string $key
	 *
	 * @return string/bool Returns value of key or false if not set
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function getKey($key = ''){
		if (!$key){
			return false;
		}
		if (self::$RUNNING === true){
			$memcache = new Memcache();
			@$memcache->connect(self::$SERVER);			
			$keyData = $memcache->get($key);
			if (empty($keyData)){
				return false;
			}
			return $keyData;
		} else {
			//return $_SESSION[$key];
		}
		return false;
	}
	
	/**
     * Removes a key from cache
	 *
	 * @param string $key	 
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function deleteKey($key){
		if (self::$RUNNING === true){
			$memcache = new Memcache();
			@$memcache->connect(self::$SERVER);
			$memcache->delete($key);
		} else {
			//unset($_SESSION[$key]);
		}
	}	
}
?>