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
 
class questiongroup{

	public static function listGroups($searchval = ''){
		$dbobject = new dbobject('poll_question_groups');
		$dbobject->join("poll","PK_PollID","FK_PollID", "LEFT JOIN");
		$dbobject->read("PK_QuestionGroupID");
		$dbobject->read("GroupName");	
		$dbobject->read("Grouped");	
		$dbobject->read("Headline");
		$dbobject->read("Priority");
		if (isset($searchval)){
			$dbobject->wildcard("Headline",$searchval);
			$dbobject->wildcard("GroupName",$searchval);		
		}			
		$dbobject->where("Language",language::get());
		$dbobject->orderby("PK_QuestionGroupID");
		return $dbobject->fetch();
	}
	
	public static function listGroupsDropDown(){
		$dbobject = new dbobject('poll_question_groups');
		$dbobject->read("PK_QuestionGroupID");
		$dbobject->read("GroupName");	
		$dbobject->where("Language",language::get());
		$dbobject->orderby("Priority");
		return $dbobject->fetch();
	}
	
	public static function readGroup($keyid){
		$dbobject = new dbobject('poll_question_groups');
		$dbobject->read("PK_QuestionGroupID");
		$dbobject->read("GroupName");
		$dbobject->read("Grouped");	
		$dbobject->read("Priority");
		$dbobject->read("FK_PollID");
		$dbobject->where("PK_QuestionGroupID",$keyid);
		return $dbobject->fetchSingle();
	}
	
	public static function readGroupOnPollID($pollid){
		$dbobject = new dbobject('poll_question_groups');
		$dbobject->read("PK_QuestionGroupID");
		$dbobject->read("GroupName");	
		$dbobject->read("Grouped");	
		$dbobject->where("FK_PollID",$pollid);
		$dbobject->orderby("Priority");
		return $dbobject->fetch();
	}	
	
	/**
	 * This method returns the min and max value of answers on a group.
	 * It is important, that min and max are the same on all questions
	 * in the group have the same min and max.
	 *
	 * @param int $groupid the groupid.
	 *
	 * @return array min and max on success.	 
	 *
	 * @access public
	 * @since Method available since Release 27-12-2014
     */	
	
	public static function readMinMaxOnGroup($groupid){
		$dbobject = new dbobject('poll_questions');
		$dbobject->join("poll_question_groups","PK_QuestionGroupID","FK_QuestionGroupID", "LEFT JOIN");
		$dbobject->read('Min');
		$dbobject->read('Max');
		$dbobject->where("FK_QuestionGroupID",$groupid);
		return $dbobject->fetchSingle();
	}	
	
	public static function createGroup($groupname,$group,$priority,$pollid){	
		$dbobject = new dbobject('poll_question_groups');
		$dbobject->create('GroupName',$groupname);
		$dbobject->create('Grouped',$group);
		$dbobject->create('Priority',$priority);	
		$dbobject->create('FK_PollID',$pollid);	
		$dbobject->create('Language',language::get());	
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		} 
		return false;			
	}	
	
	public static function updateGroup($keyid,$groupname,$group,$priority,$pollid){
		$dbobject = new dbobject('poll_question_groups');
		$dbobject->update('GroupName',$groupname);
		$dbobject->update('Grouped',$group);
		$dbobject->update('Priority',$priority);
		$dbobject->update('FK_PollID',$pollid);
		$dbobject->where("PK_QuestionGroupID",$keyid);
		return $dbobject->commit();		
	}		
	
	public static function destroyGroup($keyid){
		$dbobject = new dbobject('poll_question_groups');
		$dbobject->destroy();
		$dbobject->where("PK_QuestionGroupID",$keyid);
		return $dbobject->commit();			
	}		
}
?>