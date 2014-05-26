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

	public static function listGroups(){
		$database = new database('js_question_groups');
		return $database->read("PK_QuestionGroupID, GroupName, Priority","Language = '".$_SESSION['CountryCode']."'");
	}
	
	public static function listGroupsDropDown(){
		$database = new database('js_question_groups');
		return $database->read("PK_QuestionGroupID, GroupName","Language = '".$_SESSION['CountryCode']."'");
	}
	
	public static function readGroup($id){
		if(!$id){
			return false;
		}	
		$database = new database('js_question_groups');
		return $database->readSingle("PK_QuestionGroupID, GroupName, Priority","PK_QuestionGroupID = ".$id);
	}
	
	public static function createGroup($group,$priority){
		$database = new database('js_question_groups');
		$data = array("GroupName" => "'".$group."'","Priority" => $priority, "Language" => "'".$_SESSION['CountryCode']."'");
		var_dump($data);
		if(!$database->create($data)){
			return false;
		}	
	}	
	
	public static function updateQuestion($id,$group,$priority){
		$database = new database('js_question_groups');
		$data = array("GroupName" => "'".$group."'","Priority" => $priority, "Language" => "'".$_SESSION['CountryCode']."'");
		return $database->update($data,"PK_QuestionGroupID = ".$id);
	}		
	
	public static function destroyGroup($id){
		$database = new database('js_question_groups');
		if(!$database->destroy("PK_QuestionGroupID = ".$id)){
			return false;
		}
		return true;
	}		
}
?>