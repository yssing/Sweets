<?php

/* IMPLEMENTS SUBSET OF PHP CALENDAR FUNCTIONS ON SYSTEMS COMPILED W/O --enable-calendar */

if (!function_exists('cal_days_in_month')){
	function cal_days_in_month($a_null, $a_month, $a_year) {
		return date('t', mktime(0, 0, 0, $a_month+1, 0, $a_year));
	}
}

if (!function_exists('cal_to_jd')){
	function cal_to_jd($a_null, $a_month, $a_day, $a_year){
		if ( $a_month <= 2 ){
			$a_month = $a_month + 12 ;
			$a_year = $a_year - 1 ;
		}
		$A = intval($a_year/100);
		$B = intval($A/4) ;
		$C = 2-$A+$B ;
		$E = intval(365.25*($a_year+4716)) ;
		$F = intval(30.6001*($a_month+1));
		return intval($C+$a_day+$E+$F-1524) ;
	}
}

if (!function_exists('get_jd_dmy')) {
	function get_jd_dmy($a_jd){
		$W = intval(($a_jd - 1867216.25)/36524.25) ;
		$X = intval($W/4) ;
		$A = $a_jd+1+$W-$X ;
		$B = $A+1524 ;
		$C = intval(($B-122.1)/365.25) ;
		$D = intval(365.25*$C) ;
		$E = intval(($B-$D)/30.6001) ;
		$F = intval(30.6001*$E) ;
		$a_day = $B-$D-$F ;
		if ( $E > 13 ) {
			$a_month=$E-13 ;
			$a_year = $C-4715 ;
		} else {
			$a_month=$E-1 ;
			$a_year=$C-4716 ;
		}
		return array($a_month, $a_day, $a_year) ;
	}
}

if (!function_exists('jdmonthname')) {
	function jdmonthname($a_jd,$a_mode){
		$tmp = get_jd_dmy($a_jd) ;
		$a_time = "$tmp[0]/$tmp[1]/$tmp[2]" ;
		switch($a_mode) {
		case 0:
			return strftime("%b",strtotime("$a_time")) ;
		case 1:
			return strftime("%B",strtotime("$a_time")) ;
		}
	}
}

if (!function_exists('jddayofweek')) {
	function jddayofweek($a_jd,$a_mode){
		$tmp = get_jd_dmy($a_jd) ;
		$a_time = "$tmp[0]/$tmp[1]/$tmp[2]" ;
		switch($a_mode) {
			case 1:
				return strftime("%A",strtotime("$a_time")) ;
			case 2:
				return strftime("%a",strtotime("$a_time")) ;
			default:
				return strftime("%w",strtotime("$a_time")) ;
		}
	}
}
?>