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
	public static function parsedate($timestring,$usetime = 1,$format='d-m-Y',$stamp='Kl.:'){
		$timestring = strtotime($timestring);
		if ($usetime){
			return date($format, $timestring) ." ".$stamp." ".date("H:i", $timestring);
		} else {
			return date($format, $timestring);
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
		$retDAY = date('Y-m-d H:i:s', mktime(date('H',$cd),date('i',$cd),0,date('m',$cd),date('d',$cd)+$dayadd,date('Y',$cd)));
		return $retDAY;
	}	
	
	/**
     * This method add x amount of minutes to current datetime
	 *
	 * @return string the calculated timestamp.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function add_minutes($minutes){
		if ($minutes){
			$stamp = time() + $minutes*60; // * 60 seconds/minute;
			return date('Y-m-d H:i:s',$stamp);
		}
		return false;
	}
	
	/**
     * This method simply returns a date formatted to fit SQL
	 *
	 * @return string the current timestamp.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function now(){
		return date('Y-m-d H:i:s');
	}
	
	/**
     * This method returns current year
	 *
	 * @return string the current year.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function year(){
		return date('Y');
	}
	
	/**
     * This method returns current month
	 *
	 * @return string the current month.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function month(){
		return date('m');
	}

	/**
     * This method returns current day
	 *
	 * @return string the current day.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function day(){
		return date('d');
	}	
	
	/**
	 * This method returns a 2D array with a range of years, based on current year
	 *
	 * @param int $range The range of years, defaults to 100.
	 *
	 * @return array $list Array of years
	 *
	 * @access public
	 * @since Method available since Release 30-12-2015
     */		 
	public static function listYears($range = 100){
		$year = self::year();
		$list = array();
		for($i = 0; $i < $range; $i++){
			$list[] =  array($year-$i,$year-$i);
		}
		return $list;
	}
	
	/**
	 * This method returns a 2D array with a range of days in a given month
	 *
	 * @param int $month The month number, to find days in.
	 *
	 * @return array $list Array of days in the month
	 *
	 * @access public
	 * @since Method available since Release 30-12-2015
     */	
	public static function listDays($month = 1){
		$days = cal_days_in_month (CAL_GREGORIAN, $month, self::year());
		$list = array();
		
		for($i = 1; $i < $days+1; $i++){
			if($i < 10) {
				$x = baseclass::number_pad($i,2);
				$list[] = array($x,$x);				
			} else {
				$list[] = array($i,$i);
			}
		}
		return $list;
	}
	
	/**
	 * This method returns a 2D array with a range of months and the corresponding names
	 *
	 * @return array $list Array of months
	 *
	 * @access public
	 * @since Method available since Release 30-12-2015
     */			
	public static function listMonths(){
		$list = array();
		
		for ($i = 1; $i < 13; ++$i) {
			$list[] =  array(baseclass::number_pad($i,2),language::readType('MONTH'.$i));
		}
		return $list;
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