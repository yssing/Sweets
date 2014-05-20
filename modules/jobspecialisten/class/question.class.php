<?php
/**
 * This class handles progression keys.
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
 * @category   	Jobspecialisten methods
 * @package		Questions/Progression
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 * @require		'database.class.php'
 */
 
class question{
	
	/**
     * This method creates a progression key question.
	 *
	 * @param string $text The question.
	 * @param int $group The group.
	 * @param int $min Minimum value.
	 * @param int $max Maximum value.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */	
	public static function createQuestion($question,$group,$min = 1,$max = 10){
		$database = new database('js_questions');
		$data = array("Question" => "'".$question."'",
			"FK_GroupID" => $group,
			"Min" => $min,
			"Max" => $max,
			"Language" => "'".$_SESSION['CountryCode']."'");			
		if(!$database->create($data)){
			return false;
		}	
	}
	
	public static function listQuestions(){
		$database = new database('js_questions');
		return $database->read("PK_QuestionID, Question, FK_GroupID, Min, Max","Language = '".$_SESSION['CountryCode']."'","FK_GroupID");
	}
	
	public static function readQuestion($keyid){
		if(!$keyid){
			return false;
		}	
		$database = new database('js_questions');
		return $database->readSingle("PK_QuestionID, Question, FK_GroupID, Min, Max","PK_QuestionID = ".$keyid);
	}	
	
	public static function updateQuestion($keyid,$question,$group,$min,$max){
		$database = new database('js_questions');
		$data = array("Question" => "'".$question."'",
			"FK_GroupID" => $group,
			"Min" => $min,
			"Max" => $max);
		return $database->update($data,"PK_QuestionID = ".$keyid);
	}		
	
	/**
     * This method deletes a question.
	 *
	 * @param int $keyid The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyQuestion($keyid){
		$database = new database('js_questions');
		if(!$database->destroy("PK_QuestionID = ".$keyid)){
			return false;
		}
		return true;
	}	
	
	public static function createAnswer($keyid,$answer){
		$database = new database('js_answers');
		$data = array("FK_QuestionID" => $keyid, "Answer" => $answer);			
		if(!$database->create($data)){
			return false;
		}	
	}	
	
	public static function readAnswer($keyid){
		if(!$keyid){
			return false;
		}	
		$database = new database('js_answers');
		if($answer = $database->readSingle("answer","FK_QuestionID = ".$keyid." AND FK_UserID = ".database::$userid)){
			list($answer) = $answer;
			return $answer;
		}
		return false;
	}

	/**
     * This method deletes a question.
	 *
	 * @param int $keyid The private key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function destroyAnswer($keyid){
		$database = new database('js_answers');
		if(!$database->destroy("PK_AnswerID = ".$keyid)){
			return false;
		}
		return true;
	}	
}
?>