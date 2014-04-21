<?php include_once('calendar_functions.php')?>
<script src="calendar.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../plugins/calendar/calendar.css"  />
<?php
class calendar {

	private static function dayofweek($day_of_week){
		$blank = 0;
		switch($day_of_week){
			case "Sun": $blank = 0; break;
			case "Mon": $blank = 1; break;
			case "Tue": $blank = 2; break;
			case "Wed": $blank = 3; break;
			case "Thu": $blank = 4; break;
			case "Fri": $blank = 5; break;
			case "Sat": $blank = 6; break;
		}
		return $blank;
	}
	public static function indexAction($args){
?>
<div id="calendar" class="calendar">	
<?php
	if($_GET['datevar']){
		$date = strtotime($_GET['datevar']);
		if($_GET['datevar'] == '0000-00-00 00:00:00'){
			$date = time();
		}
	}else{
		$date = time();
	}
	//This puts the day, month, and year in seperate variables
	$day = date('d', $date);
	$month = date('m', $date);
	$year = date('Y', $date);
	
	//Here we generate the first day of the month
	$first_day = mktime(0,0,0,$month, 1, $year);

	//This gets us the month name
	$title = $month;
	//Here we find out what day of the week the first day of the month falls on
	$day_of_week = date('D', $first_day-1);

	//Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
	$blank = self::dayofweek($day_of_week); 

	//We then determine how many days are in the current month
	$days_in_month = cal_days_in_month(0, $month, $year) ;
	//Here we start building the table heads
	?>
	<table style="width:100%;" align="center" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td height="20" align="center" colspan="2">
				<?php
					$tmpmonth = $month - 1;
					$yearvar = $year;
					if(!$tmpmonth){
						$tmpmonth = 12;
						$yearvar--;
					}
				?>
				<a href="javascript:displayDate('<?php echo $_REQUEST['opener']?>','<?php echo ($yearvar."-".$tmpmonth."-01");?>',0)">
					<< <?php echo $tmpmonth;?>
				</a>
			</td>
			<td height="20" align="center" colspan="3">
				<strong><?php echo $month . " " . $year; ?></strong>
			</td>
			<td height="20" align="center" colspan="2">
				<?php
					$tmpmonth = $month + 1;
					$yearvar = $year;
					if($tmpmonth == 13){
						$tmpmonth = 1;
						$yearvar++;
					}
				?>
				<a href="javascript:displayDate('<?php echo $_REQUEST['opener']?>','<?php echo ($yearvar."-".$tmpmonth."-01");?>',0)">
					<?php echo $tmpmonth;?> >>
				</a>
			</td>
		</tr>

		<tr>
			<td width="15%" height="10" align="center">
				<strong>M</strong>
			</td>
			<td width="14%" height="10" align="center">
				<strong>T</strong>
			</td>
			<td width="14%" height="10" align="center">
				<strong>O</strong>
			</td>
			<td width="14%" height="10" align="center">
				<strong>T</strong>
			</td>
			<td width="14%" height="10" align="center">
				<strong>F</strong>
			</td>
			<td width="14%" height="10" align="center">
				<strong>L</strong>
			</td>
			<td width="15%" height="10" align="center">
				<strong>S</strong>
			</td>
		</tr>
	<?php

	//This counts the days in the week, up to 7
	$day_count = 1;

	?><tr><?php
	//first we take care of those blank days
	while ( $blank > 0 ){
		?><td height="20" valign="top" align="left"></td><?php
		$blank = $blank-1;
		$day_count++;
	}

	//sets the first day of the month to 1
	$day_num = 1;
	//count up the days, untill we've done all of them in the month
	while ( $day_num <= $days_in_month ){
		?>
			<td height="20" valign="top" align="center" id="<?php echo $_REQUEST['opener']?>D<?php echo $day_num; ?>" onmouseover="changeBackground(this.id)" onmouseout="returnBackground(this.id)" onclick="returnTime('<?php echo $_REQUEST['opener']?>','<?php echo $year?>-<?php echo $month?>-<?php echo $day_num?>')">
				<?php echo $day_num;?>
			</td>
		<?php
		$day_num++;
		$day_count++;

		//Make sure we start a new row every week
		if ($day_count > 7){
			?></tr><tr><?php
			$day_count = 1;
		}
	} //Finaly we finish the table with some blank details if needed

	while ( $day_count >1 && $day_count <=7 ){
		?><td height="20" valign="top" align="left"></td><?php
		$day_count++;
	}
	?>
		</tr>
		<tr>
			<td colspan="7" align="center"><a href="javascript:displayDate('<?php echo $_REQUEST['opener']?>','<?php echo date("Y-m-d");?>',0)">
				Aktuel måned
			</a></td>
		</tr>
		<tr>
			<td colspan="7" align="center" valign="top">
				<input class="caldate" type="text" name="inputDate<?php echo $_REQUEST['opener']?>" id="inputDate<?php echo $_REQUEST['opener']?>" value="<?php echo substr($_GET['datefield'], 0, 10);?>" onkeyup="formDate(this.id)" />&nbsp;:
				<input class="caltime" type="text" name="inputTime<?php echo $_REQUEST['opener']?>" id="inputTime<?php echo $_REQUEST['opener']?>" value="<?php echo substr($_GET['datefield'], 11, 19);?>" onkeyup="formTime(this.id)" />
				<input class="calbutton" type="button" value= "Tilføj" onclick="parseTime('<?php echo $_REQUEST['opener']?>',$('#inputDate<?php echo $_REQUEST['opener']?>').val(),$('#inputTime<?php echo $_REQUEST['opener']?>').val())" />
			</td>
		</tr>
	</table>
</div>
<?php
	
	}
}
?>