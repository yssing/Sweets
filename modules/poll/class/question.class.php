<?php
/**
 * This class handles user surveys.
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
	public static function createQuestion($question,$group,$min = 1,$max = 10,$priority){
		$dbobject = new dbobject('poll_questions');
		$dbobject->create('Question',$question);
		$dbobject->create('FK_QuestionGroupID',$group);
		$dbobject->create('Min',$min);	
		$dbobject->create('Max',$max);	
		$dbobject->create('Priority',$priority);	
		$dbobject->create('Language',language::get());	
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		} 
		return false;		
	}
	
	public static function listQuestionsOnPoll($key){
		$dbobject = new dbobject('poll_questions');
		$dbobject->join("poll_question_groups", "PK_QuestionGroupID", "FK_QuestionGroupID");
		$dbobject->join("poll", "PK_PollID", "FK_PollID");
		$dbobject->read("PK_QuestionID");
		$dbobject->where("Language",language::get());
		$dbobject->where(TPREP."poll.PollKey",$key);
		$dbobject->orderby("PK_QuestionID");
		return $dbobject->fetch();	
	}
	
	public static function listAnswersOnPoll($key, $grouped = ''){
		$dbobject = new dbobject('poll_questions');
		$dbobject->join("poll_question_groups", "PK_QuestionGroupID", "FK_QuestionGroupID");
		$dbobject->join("poll_answers", "FK_QuestionID", "PK_QuestionID");
		$dbobject->join("poll", "PK_PollID", "FK_PollID");
		$dbobject->read("Grouped");
		$dbobject->read("Answer");
		$dbobject->where("Language",language::get());
		$dbobject->where(TPREP."poll.PollKey",$key);
		$dbobject->where(TPREP."poll_answers.FK_UserID",baseclass::$userid);
		if ($grouped){
			$dbobject->where("poll_question_groups.Grouped",$grouped);
		}
		$dbobject->orderby("PK_QuestionID");
		return $dbobject->fetch();
	}	
	
	public static function listQuestions($searchval = ''){
		$dbobject = new dbobject('poll_questions');
		$dbobject->join("poll_question_groups", "PK_QuestionGroupID", "FK_QuestionGroupID");
		$dbobject->read("PK_QuestionID");
		$dbobject->read("Question");
		$dbobject->read("GroupName");
		$dbobject->read("Min");	
		$dbobject->read("Max");
		$dbobject->read(TPREP."poll_questions.Priority");
		if ($searchval){
			$dbobject->wildcard("Question",$searchval);
			$dbobject->wildcard("GroupName",$searchval);		
		}		
		$dbobject->where("Language",language::get());
		$dbobject->orderby("PK_QuestionID");
		return $dbobject->fetch();
	}

	public static function listUserQuestions($keyid){
		$final = array();
		$dbobject = new dbobject('poll_questions');
		$dbobject->read("PK_QuestionID");
		$dbobject->read("Question");
		$dbobject->read("Min");	
		$dbobject->read("Max");
		$dbobject->where("FK_QuestionGroupID",$keyid);
		$dbobject->where("Language",language::get());
		$dbobject->orderby("Priority");
		$dbobject->orderby("PK_QuestionID");
		$result = $dbobject->fetch();
		if (is_array($result)){
			foreach($result as $line){
				list($QuestionID,$Question,$Min,$Max) = $line;
				$dbobject = new dbobject('poll_answers');
				$dbobject->read("Answer");
				$dbobject->where("FK_QuestionID",$QuestionID);
				$dbobject->where("FK_UserID",baseclass::$userid);
				list($answer) = $dbobject->fetchSingle();
				$answer = ($answer) ? $answer : -127;
				$final[] = array($QuestionID,$Question,$Min,$Max,$answer);
			}
		}
		unset($dbobject);
		return $final;
	}
	
	public static function readQuestion($keyid){
		if (!$keyid){
			return false;
		}	
		$dbobject = new dbobject('poll_questions');
		$dbobject->read("PK_QuestionID");
		$dbobject->read("Question");	
		$dbobject->read("FK_QuestionGroupID");	
		$dbobject->read("Min");	
		$dbobject->read("Max");
		$dbobject->read("Priority");
		$dbobject->where("PK_QuestionID",$keyid);
		return $dbobject->fetchSingle();
	}	
	
	public static function updateQuestion($keyid,$question,$group,$min,$max,$priority){
		$dbobject = new dbobject('poll_questions');
		$dbobject->update('Question',$question);
		$dbobject->update('FK_QuestionGroupID',$group);
		$dbobject->update('Min',$min);
		$dbobject->update('Max',$max);
		$dbobject->update('Priority',$priority);
		$dbobject->where("PK_QuestionID",$keyid);
		return $dbobject->commit();
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
		$dbobject = new dbobject('poll_questions');
		$dbobject->destroy();
		$dbobject->where("PK_QuestionID",$keyid);
		return $dbobject->commit();				
	}	
	
	public static function createAnswer($keyid,$answer){
		$dbobject = new dbobject('poll_answers');
		$dbobject->create('Answer',$answer);
		$dbobject->create('FK_QuestionID',$keyid);
		if (!$dbobject->commit()){
			return false;
		} 
		return true;
	}	
	
	public static function readAnswer($keyid){
		$dbobject = new dbobject('poll_answers');
		$dbobject->read("Answer");
		$dbobject->where("FK_QuestionID",$keyid);
		$dbobject->where("FK_UserID",baseclass::$userid);
		return $dbobject->fetchSingle();		
	}
	
	public static function updateAnswer($keyid,$answer){
		$dbobject = new dbobject('poll_answers');
		$dbobject->update('Answer',$answer);
		$dbobject->where("FK_QuestionID",$keyid);
		$dbobject->where("FK_UserID",baseclass::$userid);
		if (!$dbobject->commit()){
			return false;
		} 
		return true;
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
		$dbobject = new dbobject('poll_answers');
		$dbobject->destroy();
		$dbobject->where("PK_AnswerID",$keyid);
		return $dbobject->commit();			
	}
	
	/**
	 * This method saves answers on a poll
	 *
	 * @param array $args all the arguments from the autorouter.
	 *
	 * @return true.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function answerPoll($args){
		// question logick
		$questionlist = self::listQuestions();
		foreach($questionlist as $single){
			list($questionid) = $single;
			if (isset($args['question'.$questionid])){
				list($answer) = self::readAnswer($questionid);
				if ($answer){
					self::updateAnswer($questionid,$args['question'.$questionid]);
				} else {
					self::createAnswer($questionid,$args['question'.$questionid]);
				}
			}
		}
		return true;
	}	
	
	/**
	 * This method creates a radio button poll
	 *
	 * @param string $name The name of the poll's radio buttons.
	 * @param string $question The question text.
	 * @param int $start The start value.
	 * @param int $end The end value.
	 * @param int $selected The value of the preselected option, if given.
	 *
	 * @return string The radio button poll.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public static function createPollRadio($name,$question,$start,$end,$selected = ''){
		$data = '
			<tr>
				<td>
					'.$question.'
				</td>';
		for ($i = $start; $i <= $end; $i++){
			if ($i == intval($selected)){
				$selectBool = form::returnChecked(1);
			} else {
				$selectBool = '';
			}
			$data .= '
				<td style="width:25px;padding:10px;">
					<input type="radio" name="'.$name.'" id="'.$name.'_'.$i.'" value="'.$i.'" '.$selectBool.'>
				</td>';
		}
		$data .= '</tr>';	
		return $data;
	}
	
	/**
	 * This method creates a checkbox poll
	 *
	 * @param string $name The name of the poll's check boxes.
	 * @param string $question The question text.
	 * @param int $selected The value of the preselected option, if given.
	 *
	 * @return string The checkbox button poll.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */			
	public static function createPollCheck($name,$question,$selected = 0){
		$data = '
		<table class="col-xs-12">
			<tr>
				<td>
					'.$question.'
				</td>
				<td style="width:25px;padding:10px;">
					'.form::check($selected,$name).'
				</td>
			</tr>
		</table>';	
		return $data;
	}	
}
?>