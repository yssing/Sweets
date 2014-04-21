<?php
/**
 * This class handles different kind of methods and variables, 
 * that are not related to any kind of user display. 
 * These methods and variables are used extensively throughout the portal. 
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
 * @package		genericIO
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 */

class genericIO{
	/**
     * if admin session is set, then this variable will always hold the PK value
	 *
     * @var int adminid
     * @access protected
     */	
	public static $adminid = 0;
	
	/**
     * if user session is set, then this variable will always hold the PK value
	 *
     * @var int userid
     * @access protected
     */	
	public static $userid = 0;	
	
	/**
     * The error_reporting() variable 
	 *
     * @var int ERROR_REPORT
     * @access public
	 * @static
     */		
	public static $ERROR_REPORT = ERROR_REPORT;
	
	/**
     * If the error has to be saved in a text file, set this to 1. 
	 *
     * @var bool GENERATE_REPORT
     * @access public
	 * @static
     */
	public static $GENERATE_REPORT = LOG_ERROR;
	
	/**
     * If the error has to be sent as an e-mail, set this to 1. 
	 *
     * @var bool SEND_ERROR
     * @access public
	 * @static
     */		
	public static $SEND_ERROR = 0;
	
	/**
     * Holds the error message that an user can see. 
	 *
     * @var string ERROR_MESSAGE
     * @access public
	 * @static
	 * @since Method available since Release 1.0.0
     */		
	public static $ERROR_MESSAGE;

	/**
     * The constructor handles initializing session, error handling and 
	 * if either the user or admin session is available, it sets those varibles
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function __construct(){
		error_reporting(self::$ERROR_REPORT);
		self::setSession();
		if(isset($_SESSION['adminSession'])){
			self::$adminid = $_SESSION['adminSession']['AdminID'];
		}
		if(isset($_SESSION['userSession'])){
			self::$userid = $_SESSION['userSession']['UserID'];
		}
	}	

	/**
     * The destructor
	 *
     * @access public
     */		
	public function __destruct(){
		/* */
	}

	/**
     * If the session is not available, it creates it.
	 * This method is only used in this class.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */	
	public function setSession(){
		if (strlen(session_id()) < 1) {
			session_start();
		}		
	}
	
	/**
	 * Method to truncate text
	 *
	 * @param string $text the text to truncate
	 * @param int $chars number af chars to show
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	*/
	public static function truncate($text, $chars = 25) {
		$text = $text." ";
		$text = substr($text,0,$chars);
		$text = substr($text,0,strrpos($text,' '));
		$text = $text." ...";
		return $text;
	}	
		
	/**
	 * Replaces special letters with the appropriate escape strings.
	 *
	 * @param string $string the string to be formatted.   
	 *
	 * @return string the formatted string.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function escapeChars($string){
		$string = str_replace('æ', '&#230;', $string);
		$string = str_replace('ø', '&#248;', $string);
		$string = str_replace('å', '&#229;', $string);
		$string = str_replace('Æ', '&#198;', $string);
		$string = str_replace('Ø', '&#216;', $string);
		$string = str_replace('Å', '&#197;', $string);
		$string = str_replace('"', '&#34;', $string);    // baseline double quote
		$string = str_replace(';', '&#59;', $string);    // baseline semi colon
		$string = str_replace("'", '&#39;', $string);    // baseline single quote		
		$string = trim($string);
		return $string;
	}	
	
	/**
	 * Because urlencode does not do the job I need it to do.
	 *
	 * @param string $string the string to be formatted.   
	 *
	 * @return string the formatted string.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function encode($string){
		$string = str_replace('.', '%2E', $string);
		$string = str_replace(' ', '%20', $string);
		$string = str_replace('/', '%26', $string);
		return $string;
	}

	/**
	 * Because urldecode does not do the job I need it to do.
	 *
	 * @param string $string the string to be formatted.   
	 *
	 * @return string the formatted string.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public function decode($string){
		$string = str_replace('%2E', '.', $string);
		$string = str_replace('%20', ' ', $string);
		$string = str_replace('%26', '/', $string);
		return $string;
	}
	
	/**
	 * This method handles the debugging information.
	 *
	 * If the ERROR_REPORT is true, the method will display a footer with some basic
	 * debugging messages, this footer will have a timestamp and display what ever error was thrown.
	 * It can also display an sql string and the name of the method that failed.
	 *
	 * @param string $string the string used in for debugging.   
	 * @param string $sql a given sql string that needs to be displayed.   
	 *	 
	 * @access protected
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function DBug($string,$method = '',$sql = ''){
		$style = 'position:relative;height:60px;float:left;margin-left:5px;';
		$timestring = time();
		$datestring = date("d-m-Y", $timestring) ." Kl.: ".date("H:i", $timestring);
		if(self::$ERROR_REPORT){
			echo '<div class="system_error">
			<div style="'.$style.'width:160px;">'.$datestring.'</div>
			<div style="'.$style.'width:320px;"><i>'.$string.'</i></div>
			<div style="'.$style.'width:180px;">Method: '.$method.'()</div>
			<div style="'.$style.'width:800px;">SQL: <i>'.$sql.'</i></div>
			</div>';
		} 
		$string = $datestring. " " .$string;
		if(self::$GENERATE_REPORT){
			self::checkfolder("","debug");
			$myFile = 'debug/debug.txt';
			$current = file_get_contents($myFile);
			$current .= $string."\r\n";
			$current .= $sql."\r\n";
			file_put_contents($myFile, $current);
		}		
		if(self::$SEND_ERROR){
			self::sendMailToUser(SITENAME,SITEMAIL,SITEMAIL,PATH_WEB.' - fejl',$string);
		}
	}
	
	/**
	 * This method handles the user view of the error message.
	 *
	 * @param string $message the error message.   
	 * @param bool $red red text or not.   
	 * @param string $class the css class used to style the error message.   
	 *	 
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function errMsg($message,$red = 0,$class = 'non'){
		if($message){
			if($red){
				self::$ERROR_MESSAGE = '<h3 class="'.$class.'" style="color:#aa0000;">'.$message.'</h3>';
			} else {
				self::$ERROR_MESSAGE = '<h3 class="'.$class.'">'.$message.'</h3>';
			}
		}
	}	
		
	/**
	 * This method generates a new random string.
	 *
	 * @param int $length the length of the string to be generated.   
	 *
	 * @return string $randstr the generated string.
	 *	 
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function generateRandStr($length){
		$randstr = "";
		for($i=0; $i<$length; $i++){
			$randnum = mt_rand(0,61);
			if($randnum < 10){
				$randstr .= chr($randnum+48);
			}else if($randnum < 36){
				$randstr .= chr($randnum+55);
			}else{
				$randstr .= chr($randnum+61);
			}
		}
		return $randstr;
	}		
	
	/**
	 * This method returns the extension of a file.
	 *
	 * @param string $file the file to get the extension name from.   
	 *
	 * @return string the file extension.
	 *	 
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function getFileExtension($file){
		$tmpArray = explode(".",$file);
		$size = count($tmpArray);
		return strtolower($tmpArray[$size-1]);
	}
	
	/**
	 * This method checks if a folder exsist.
	 * If not, then create the folder.
	 *
	 * @param string $path the relative path to the folder.   
	 * @param string $name the name of the folder.   
	 *
	 * @return bool Returns TRUE on success or FALSE on failure.
	 *	 
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function checkfolder($path,$name){
		if (!is_dir($path.$name)){
			return mkdir($path.$name,0777);
		}
	}	
	
	/**
	 * This method deletes a folder and the subfolders.
	 *
	 * The method recursively checks a folder for any subfolders
	 * and subfiles and then it deletes then in order until all 
	 * folders and files have been deleted.
	 * If the folder is a file and not a folder, it simply deletes the file
	 *
	 * @param string $folder the relative path to the folder.     
	 *	 
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function rrmdir($folder) {
		if (is_dir($folder)) {
			$objects = scandir($folder);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($folder."/".$object) == "dir"){ 
						self::rrmdir($folder."/".$object);
					} else {
						unlink($folder."/".$object);
					}
				}
			}
			reset($objects);
			rmdir($folder);
		} else {
			unlink($folder);
		}
		return true;
	} 
	
	/**
	 * This method sends an e-mail to an entered e-mail.
	 * 
	 * The method formats the e-mail in html and encoded in utf-8.
	 * It also sets the appropriate headers
	 * The e-mail is only send if the define ENVIRONMENT is true 
	 *
	 * @param string $senderName the senders name.  
	 * @param string $senderMail the senders e-mail.
	 * @param string $receiverMail the receivers e-mail.
	 * @param string $subject the subject of the e-mail.
	 * @param string $msg the body/message of the e-mail.
	 *
	 * @return bool $retval Returns TRUE on success or FALSE on failure.
	 *	 
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public function sendMailToUser($senderName,$senderMail,$receiverMail,$subject,$msg){
		$retval = 0;
		$message = '
		<html xmlns="http://www.w3.org/1999/xhtml"> 
			<head>
				<meta http-equiv="Content-Language" content="da" />
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>'.$subject.'</title>
			</head>
			<body>
				<p>'.$msg.'</p>
			</body>
		</html>';

		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'From: '.$senderName.' <'.$senderMail.'>' . "\r\n";
		
		// Mail it only if on live server
		if(ENVIRONMENT == "LIVE"){
			try{
				$retval = mail($receiverMail, $subject, $message, $headers);
				if(!$retval){
					throw new Exception('Could not send message to mail: '. $receiverMail);
				} 
			}
			catch (Exception $e){
				self::DBug('Caught exception: '. $e->getMessage(). ' in method: '.__METHOD__.' in file: '.__FILE__);
			}
		}
		return $retval;
	}
}
?>