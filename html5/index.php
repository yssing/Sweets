<?php
	class HighScore {	
		public function __construct(){
			session_start();
			error_reporting(-1);
			
			if($_SERVER['HTTP_HOST'] == 'localhost'){
				mysql_connect("localhost","root","");
				@mysql_select_db("new_system") or die("Could not connect: " . mysql_error());
			} else {
				mysql_connect("mysql17.cliche.dk","yssing.org","qa62zmfs");
				@mysql_select_db("yssing_org") or die("Could not connect: " . mysql_error());
			}
			date_default_timezone_set('UTC'); 
			setlocale(LC_ALL,'nld_nld');				
		}
	
		public function stripchars($string){
			$string = str_replace('"', '&#34', $string);    // baseline double quote
			$string = str_replace(';', '&#59', $string);    // baseline semi colon
			$string = trim($string);
			return $string;
		}	
	
		public function displayListview($data){
			$table = '';
			$i = 0;
			$table .=  '<table border="0" class="listview" cellpadding="0" cellspacing="0">';
			foreach($data as $row){
				if(!$i){
					$table .=  '<tr style="background:#efefef">';	
					$i = 1;
				} else {
					$table .=  '<tr style="background:#ffffff">';
					$i = 0;
				}		
				foreach($row as $tddata){
					$table .=  '<td>';
					$table .=  $tddata.'';
					$table .=  '</td>';
				}
				$table .=  '</tr>';
			}
			$table .=  '</table>';
			return $table;
		}
		
		public function parsedate($timestring){
			$timestring = strtotime($timestring);
			return date("d-m-Y", $timestring) ." Kl.: ".date("H:i", $timestring);
		}			
		
		public function createEntry($points,$name){
			$points = self::stripchars($points);
			$name = self::stripchars($name);
			$returnval = "";
			$sql = "INSERT INTO game (Name, Points, Date) VALUES ('".$name."',".$points.",NOW())";
			try{
				if(!mysql_query($sql)){
					throw new Exception('Could not save name and score');
				}
			}
			catch (Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "<br />";
			}	
			return $returnval;
		}
		
		public function readEntries($numbers = 25){
			$numbers = self::stripchars($numbers);
			$data = array();
			$sql = "SELECT * FROM game ORDER BY Points DESC LIMIT ".$numbers;
			$result = mysql_query($sql);
			try{
				if(!$result){
					throw new Exception('Could not read highscore table');
				} else {
					$data[] = array('<h4 style="width:240px;">Name</h4>','<h4 style="width:80px;">Points</h4>','<h4 style="width:180px;">Date saved</h4>');
					while($row = mysql_fetch_array($result)){
						$data[] = array($row['Name'],$row['Points'],self::parsedate($row['Date']));
					}	
				}
			}
			catch (Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "<br />";
			}	
			return $data;			
		}
	}
	
	$errormessage = "";	
	$val = "";
	$val1 = "";
	$highscore = new HighScore();
	
	if(isset($_REQUEST['submit'])){		
		$errormessage = $highscore->createEntry($_REQUEST['points'],$_REQUEST['name']);
		header( 'Location: index.php');
	}
	$val = $highscore->readEntries();
	if($val){
		$val1 = $highscore->displayListview($val);
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Alien Air Attack</title>
	</head>	
	<body onload="setup()" background="stars.jpeg">
		<div style="margin:auto;border: 2px solid #345;width:500px;background-color:#fff;">
			<a href="shooter.html"><img src="Title.jpg" border="0" /></a>
			A game developed in javascript, using OOP.
			<br />
			<b>Programming by:</b> Frederik Yssing
			<br />
			<b>Graphics by:</b> Kevin Saunders aka Invent
			<br />
			<b>Music:</b> Dream Of Penguins by Krister Skrtic www.psykicko.com
			<br />
			<?php echo $errormessage;?>
			<?php echo $val1;?>
			<br />
			<?php if(isset($_REQUEST['points'])){?>
			<form action="index.php" method="post">
				<fieldset style="width:440px;">
					<legend>
						<h3>Congratulations, you scored: <?php echo $_REQUEST['points'];?> points!</h3>
					</legend>
					Enter your name here: <input style="width:240px;" type="text" name="name" value="" />
					<input type="hidden" name="points" value="<?php echo $_REQUEST['points'];?>" />
					<br /><br />
					<input class="button" type="submit" name="submit" id="btnSubmit" value="Save highscore" />
				</fieldset>
			</form>
			<?php } ?>
		</div>		
		<br />
		<table style="margin:auto;border: 2px solid #345;width:500px;background-color:#fff;">
			<!--tr><td style="text-align:center;"><img src="ship.png"></td><td>This is you, move and shoot with mouse or arrow and space.<br /><i>Be carefull when holding space down, if you die and space is pressed you might reset the game.</i></td></tr>
			<tr><td style="text-align:center;"><img src="bullets.png"></td><td>Use your bullets to shoot down the aliens, can be upgraded with bonus drops.</td></tr>
			<tr><td style="text-align:center;"><img src="enemy.png"></td><td>Shoot these to get points and bonus drops, they shoot back. Collide and you die!</td></tr>
			<tr><td style="text-align:center;"><img src="enemyshot.png"></td><td>Avoid them, they will kill you!</td></tr>
			<tr><td style="text-align:center;"><img src="life.png"></td><td>Adds an extra life, grab it!</td></tr>
			<tr><td style="text-align:center;"><img src="smallshield.png"></td><td>Gives you immunity for a short time, shields are stackable!</td></tr>
			<tr><td style="text-align:center;"><img src="mediumshield.png"></td><td>Gives you immunity for a little longer, shields are stackable!</td></tr>
			<tr><td style="text-align:center;"><img src="largeshield.png"></td><td>Gives you immunity for a longer time, shields are stackable!</td></tr>
			<tr><td style="text-align:center;"><img src="point1.png"></td><td>Adds 500 extra points</td></tr>
			<tr><td style="text-align:center;"><img src="point2.png"></td><td>Adds 200 extra points</td></tr>
			<tr><td style="text-align:center;"><img src="point3.png"></td><td>Adds 100 extra points</td></tr>
			<tr><td style="text-align:center;"><img src="point4.png"></td><td>Adds 50 extra points</td></tr>
			<tr><td style="text-align:center;"><img src="point5.png"></td><td>Adds 20 extra points</td></tr>
			<tr><td style="text-align:center;"><img src="point6.png"></td><td>Adds 10 extra points</td></tr>
			<tr><td style="text-align:center;"><img src="powerup.png"></td><td>Adds 25 extra to shot damage, grab it to kill aliens faster!</td></tr-->
			<tr><td style="text-align:center;"></td>
				<td>					
				<form  action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="5ZF9JY7T3JBY8">
					<input class="donate" type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
					<img alt="" border="0" src="https://www.paypalobjects.com/da_DK/i/scr/pixel.gif" width="1" height="1">
				</form>	Donations are very welcome! and will be shared with team members</td>
			</tr>
		</table>
		<br />	
	</body>
</html>