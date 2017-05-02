<?php
/**
 * This class handles all the different kind of views and dataformating that
 * are used extensively throughout the portal.
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
 * @package		views
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 * @require		'generic.io.class.php'
 */
include_once('baseclass.class.php');
class views extends baseclass{

	public static $pagination = '';

	/**
	 * Creates a 2D array with sequential numbers.
	 *
	 * @param int $size the relative path to the folder.
	 *
	 * @return array $data Returns a filled 2D array.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function fillArray($size){
		$data = array();
		for($j = 1;$j <= $size; $j++){
			$data[] = array($j,$j);
		}
		return $data;
	}

	/**
	 * Displays a 2D array in a JSON file, for now it just works as a wrapper for the json_encode
	 *
	 * @param array $data the data to format and display.
	 *
	 * @return string $json the json formatted list.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function displayJSON($data){
		return json_encode($data);
	} 
	
	/**
	 * Displays a 2D array in a listview.
	 *
	 * If the error_reporting mode is active, then the method will add a carriage return
	 * after each td and tr.
	 *
	 * @param array $data the data to format and display.
	 * @param string $link An action for click on a row.
	 * @param string $color use changing background color or not.
	 * @param integer $pagesize used with the paging
	 * @param string $class the css class used to style the dropdown.
	 *
	 * @return string $table the formatted listview.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function displayListview($data, $link = '', $color = 1, $pagesize = PAGING, $class = 'listview'){
		$table = '';
		$i = 0;
		$y = 0;
		$page = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
		$from = ($page * $pagesize) - $pagesize;
		$to = $from + $pagesize;
		if (is_array($data)){
			$table .= '<table border="0" class="table table-hover '.$class.'" cellpadding="0" cellspacing="0">';
			foreach($data as $row){
				$y++;
				if ($y >= $from && $y < $to){
					$table .= '<tr>';
					foreach($row as $tddata){
						$table .= '<td>';
						if ($link){
							$table .= '<a href="/'.$link.'/'.$row[0].'">';
							$table .= '&nbsp;'.$tddata.'&nbsp;';
							$table .= '</a>';
						} else {
							$table .= '&nbsp;'.$tddata.'&nbsp;';
						}
						$table .= '</td>';
						if (self::$ERROR_REPORT){
							$table .= chr(13);
						}
					}
					$table .= '</tr>';
					if (self::$ERROR_REPORT){
						$table .= chr(13);
					}
				}
			}
			$table .= '</table>';
		}
		$table .= self::showPaging($y,$page,$pagesize,5);
		return $table;
	}

	/**
	 * Displays a 2D array in a listview with edit and delete options.
	 *
	 * @param array $data the data to format and display.
	 * @param string $class the css class used to style the dropdown.
	 * @param integer $showadd Show the add buttons and paging.
	 * @param integer $pagesize used with the paging
	 *
	 * @return string $table the formatted listview.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function displayEditListview($data,$class = 'listview',$showadd = 1,$pagesize = PAGING,$settings = ''){
		$url = route::getBaseURL();
		$url = str_replace('/list','',$url);
		$url = PATH_WEB.'/'.$url;

		$table = '';
		$view = '';
		$i = 0;
		$y = 0;
		$page = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
		$from = ($page * $pagesize) - $pagesize;
		$to = $from + $pagesize;
		
		if(is_array($data)){		
			$view .= '<table border="0" class="table table-hover '.$class.'" cellpadding="0" cellspacing="0">';
			foreach($data as $row){
				$y++;
				if ($y >= $from && $y < $to){
					$view .= '<tr>';
					foreach($row as $tddata){
						$view .= '<td> '.$tddata.' </td>';
						
						if (self::$ERROR_REPORT){
							$view .= chr(13);
						}
					}
					if (!empty($row[0])){
						$view .= '<td width="60">&nbsp;<a class="btn btn-primary btn-xs" href="'.$url.'/edit/'.$row[0].'">'.language::readType('EDIT').'</a>&nbsp;</td>';
					}
					if (self::$ERROR_REPORT){
						$view .= chr(13);
					}
					if (!empty($row[0])){
						$view .= '<td width="60">&nbsp;<a class="btn btn-danger btn-xs" href="javascript:confirmDel(\''.$url.'/delete/'.$row[0].'\')">'.language::readType('DELETE').'</a>&nbsp;</td>';
					}
					if (self::$ERROR_REPORT){
						$view .= chr(13);
					}
					
					$view .= '</tr>';
					if (self::$ERROR_REPORT){
						$view .= chr(13);
					}
				}
			}
			$view .= '</table>';
		}
		
		$paging = self::showPaging($y,$page,$pagesize,5,$settings);	
		
		// only show find form, if there are multiple pages
		$searchVal = (isset($_REQUEST['searchfield'])) ? $_REQUEST['searchfield'] : '';
		if ($paging || $searchVal){
			$table .= form::findForm($searchVal);
		}
		
		if ($showadd){
			$table .= form::newButton();		
			$table .= $paging;	
			$table .= '<br>'.$view;
			$table .= form::newButton();
			$table .= $paging;
		} else {
			$table .= $paging;
			$table .= '<br>'.$view;
			$table .= $paging;
		}
		return $table;
	}
	
	/**
	 * Creates a paging list.
	 *
	 * @param integer $total the number of pages.
	 * @param integer $page the page that is shown.
	 * @param integer $pagesize how many items per page.
	 * @param integer $range The maximum of pages before and after current page.
	 *
	 * @return string the string prepended with 0.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function showPaging($total,$page = 1,$pagesize = PAGING,$range = 5,$settings=''){
		if (self::$pagination){
			return self::$pagination;
		}
		
		$url = baseclass::curPageURL();
		$arguments = '';
		if (is_array($settings)){
			foreach($settings as $key => $val){
				$arguments .= '&'.$key.'='.$val;
			}
		}
		
		if (isset($_REQUEST['searchfield'])){
			$arguments .= '&searchfield='.$_REQUEST['searchfield'];
		}
		
		$lastPage = ceil($total / $pagesize);
		if ($page > 1) {
			$start = $page;	
		} else {
			$start = 1;
		}
		$pagination = '';
		// loop to show links to range of pages around current page
		for ($x = ($page - $range); $x < (($page + $range + 1)); $x++) {
			// if it's a valid page number...
			if (($x > 0) && ($x <= $lastPage)) {

				// if we're on current page...
				if ($x == $page) {
					if ($x > 1){
						$previous = $x-1;
					} else {
						//$previous = 1;
						$previous = ceil($total / $pagesize);
					}
					if ($x < $lastPage){
						$next = $x+1;
					} else {
						//$next = $lastPage;
						$next = 1;
					}
					
					// 'highlight' it but don't make a link
					$pagination .= ' <li class="active"><span>'.$x.'</span></li> ';
					// if not current page...
				} else {
					// make it a link
					$pagination .= '<li><a href="'.$url.'/?page='.$x.$arguments.'">'.$x.'</a></li>';
				}
			}
		}
		
		if ($total > $pagesize){
			$output = '<ul class="pagination">';
			$output .= '<li><a href="'.$url.'/?page=1'.$arguments.'"><<</a></li>';
			$output .= '<li><a href="'.$url.'/?page='.$previous.$arguments.'"> < </a></li>';
			$output .= $pagination;
			$output .= '<li><a href="'.$url.'/?page='.$next.$arguments.'"> > </a></li>';
			$output .= '<li><a href="'.$url.'/?page='.$lastPage.$arguments.'">>></a></li>';
			$output .= '<li class="active"><span> Af: '.$lastPage.' </span></li></ul>';
		} else {
			$output = '';
		}
		self::$pagination = $output;
		return $output;
	}
	
	/**
	 * prepend a number with n amounts of 0.
	 *
	 * @param string $number the number to be prepended.
	 * @param int $n the the maximum amount of 0 to be prepended.
	 *
	 * @return string the string prepended with 0.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function number_pad($number,$n){
		return str_pad(intval($number),$n,"0",STR_PAD_LEFT);
	}
	
	/**
	 * Seperates a number with thousand seperator.
	 *
	 * @param int $number the number to be formatted. 
	 * @param int $decimals the number of decimals. 
	 *
	 * @return string the formatted string.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function TSeperator($number,$decimals = 0){
		return number_format($number, $decimals, ',', '.');
	}

	/**
	 * Formats a thousand seperated number/string.
	 * So it can be used in regular math
	 *
	 * @param int $number the number to be formatted.
	 *
	 * @return number the formatted number.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function formatNumber($number){
		$number = str_replace(".", "", $number);
		$number = str_replace(",", ".", $number);
		return $number;
	}
	
	/**
	 * Formats a number prepended with a currency symbol.
	 * This method uses the TSeperator in this class.
	 *
	 * @param int $number the number to be formatted.  
	 * @param string $currencySymbol the currency symbol used.  
	 * @param int $decimals the number of decimals.  
	 *
	 * @return string the formatted number.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function moneyFormat($number, $currencySymbol = '$', $decimals = 2){
		return $currencySymbol.self::TSeperator($number,$decimals);
	}
}
?>