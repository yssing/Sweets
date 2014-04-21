<?php
/**
 * This class handles different methods for date presentation and handling.
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
 * @package    	calendar
 * @author     	Frederik Yssing <yssing@yssing.org>
 * @copyright  	2012-2014 Yssing
 * @version    	SVN: 1.0.0
 * @link       	http://www.yssing.org
 * @since      	File available since Release 1.0.0
 * @require		'language.class.php'
 */

class calendar {
	
	/**
     * This method calculates the difference between two dates
	 *
	 * @param date $beginDate The date to start from.
	 * @param date $endDate The date to end the calculation.
	 *
	 * @return int the number of days between the 2 dates.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function daysDifference($beginDate, $endDate){
		$beginDate = strtotime($beginDate);
		$endDate = strtotime($endDate);
		return floor(abs($endDate - $beginDate) / 86400);
	}

	/**
     * This method presents a given date in danish format
	 *
	 * @param date $timestring The date to reformat.
	 * @param int $usetime show time and date or just the time.
	 *
	 * @return string the formatted date string.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function parsedate($timestring,$usetime = 1){
		$timestring = strtotime($timestring);
		if($usetime){
			return date("d-m-Y", $timestring) ." Kl.: ".date("H:i", $timestring);
		} else {
			return date("d-m-Y", $timestring);
		}
	}	

	/**
     * This method calculates a future date by adding
	 * number of days to a current date.
	 *
	 * @param date $orgDate The date to add to.
	 * @param int $dayadd The number af days to add.
	 *
	 * @return string $retDAY The future date string.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function add_date($orgDate,$dayadd){
		$cd = strtotime($orgDate);
		$retDAY = date('Y-m-d H:i', mktime(date('H',$cd),date('i',$cd),0,date('m',$cd),date('d',$cd)+$dayadd,date('Y',$cd)));
		return $retDAY;
	}	
	
	/**
     * This method finds the day name of a given date.
	 *
	 * @param date $timestring The date where the day has to be found.
	 *
	 * @return string $dayname The name of the day found.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 * @requires language class
     */		
	public static function whatDay($timestring){
		$dayofweek = date("N", strtotime($timestring));
		$dayname = '';
		switch($dayofweek){
			case 0: 
				$dayname = language::readType('DAY1'); 
				break;
			case 1: 
				$dayname = language::readType('DAY2');
				break;
			case 2: 
				$dayname = language::readType('DAY3');
				break;
			case 3: 
				$dayname = language::readType('DAY4'); 
				break;
			case 4: 
				$dayname = language::readType('DAY5'); 
				break;
			case 5: 
				$dayname = language::readType('DAY6'); 
				break;
			case 6: 
				$dayname = language::readType('DAY7'); 
				break;
		}
		return $dayname;
	}
	
	/**
     * This method finds the month name of a given date.
	 *
	 * @param date $timestring The date where the month has to be found.
	 *
	 * @return string $monthname The name of the month found.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 * @requires language class
     */		
	public static function whatMonth($timestring){
		$month = date("n", strtotime($timestring));
		$monthname = '';
		switch($month){
			case 1: 
				$monthname = language::readType('MONTH1');
				break;
			case 2: 
				$monthname = language::readType('MONTH2');
				break;
			case 3: 
				$monthname = language::readType('MONTH3'); 
				break;
			case 4: 
				$monthname = language::readType('MONTH4'); 
				break;
			case 5: 
				$monthname = language::readType('MONTH5'); 
				break;
			case 6: 
				$monthname = language::readType('MONTH6'); 
				break;
			case 7: 
				$monthname = language::readType('MONTH7');
				break;
			case 8: 
				$monthname = language::readType('MONTH8');
				break;
			case 9: 
				$monthname = language::readType('MONTH9'); 
				break;
			case 10: 
				$monthname = language::readType('MONTH10'); 
				break;
			case 11: 
				$monthname = language::readType('MONTH11'); 
				break;
			case 12: 
				$monthname = language::readType('MONTH12'); 
				break;				
		}
		return $monthname;		
	}
}
?>