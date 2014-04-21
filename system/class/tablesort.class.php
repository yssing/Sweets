<?php
/**
 * This class simply sort an array based on a specific column.
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
 * @package		forms
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 */

class tablesort {

	/**
	 * the column in the array to sort on.
	 *
	 * @access protected
	 * @static
	 */
	protected static $column;
	
	/**
	 * This method sorts an array based on a specific column in a 2D array.
	 *
	 * If static is not wanted, then the 'self' has to changed to $this!
	 *
	 * @param array $table The 2D array we want to sort.  
	 * @param int $column The column to sort on in the array.  
	 * @param int $order What order to sort in. 1 = HighToLow, !1 = LowToHigh  
	 *
	 * @return array the sorted array.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */			
	public static function sort($table,$column,$order = 1) {
		self::$column = $column;
		if($order == 1){
			usort($table, array(self, 'highToLow'));
			return $table;
		} else {
			usort($table, array(self, 'highToLow'));
			return $table;
		}	
	}

	/**
	 * The hightolow comparator
	 */	
	protected static function highToLow($b, $a) {
		if ($a[self::$column] == $b[self::$column]) {
			return 0;
		}
		return ($a[self::$column] < $b[self::$column]) ? -1 : 1;
	}
	
	/**
	 * The lowtohigh comparator
	 */		
	protected static function lowToHigh($a, $b) {
		if ($a[self::$column] == $b[self::$column]) {
			return 0;
		}
		return ($a[self::$column] < $b[self::$column]) ? -1 : 1;
	}	
}
?>