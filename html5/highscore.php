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
				@mysql_select_db("new_system") or die("Could not connect: " . mysql_error());
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
			echo $sql;
			try{
				if(!mysql_query($sql)){
					throw new Exception('Could not save name and score');
				}
			}
			catch (Exception $e){
				echo 'Caught exception: ',  $e->getMessage(), "<br />";
				//$returnval = 'Caught exception: '.  $e->getMessage();
			}	
			return $returnval;
		}
		
		public function readEntries($numbers = 25){
			$numbers = self::stripchars($numbers);
			$data = array();
			$sql = "SELECT * FROM game ORDER BY PK_GameID DESC LIMIT ".$numbers;
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
	}
	$val = $highscore->readEntries();
	if($val){
		$val1 = $highscore->displayListview($val);
	} 
?>
<!DOCTYPE html>
<html>
	<head>
	<body onload="setup()">
		<br />
		<a href="starfieldplayer.html">Play the game</a> <i>currently not finished!</i>
		<?php echo $errormessage;?>
		<?php echo $val1;?>
		<br />
		<?php if(isset($_REQUEST['points'])){?>
		<form action="highscore.php" method="post">
			<fieldset style="width:440px;">
				<legend>
					<h3>Congratulations, you scored: <?php echo $_REQUEST['points'];?> points!</h3>
				</legend>
				Enter your name here: <input style="width:240px;" type="text" name="name" value="" />
				<input type="text" name="points" value="<?php echo $_REQUEST['points'];?>" />
				<br /><br />
				<input class="button" type="submit" name="submit" id="btnSubmit" value="Save highscore" />
			</fieldset>
		</form>
		<?php } ?>
		<br />		
	</body>
</html>