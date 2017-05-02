<?php
class aaa{
	public static function listScores($admin = 0){
		$dbobject = new dbobject('aaa_scores');
		$dbobject->read("Name");
		$dbobject->read("Points");
		$dbobject->orderby("Points", "DESC");
		return $dbobject->fetch();		
	}

	public static function listAdminScores($admin = 0){	
		$dbobject = new dbobject('aaa_scores');
		$dbobject->read("PK_ScoreID");
		$dbobject->read("Name");
		$dbobject->read("Points");
		$dbobject->read("CreateDate");
		$dbobject->orderby("Points", "DESC");
		return $dbobject->fetch();
	}
	
	public static function saveScore($name,$score){
		$dbobject = new dbobject('aaa_scores');
		$dbobject->create('Name',$name);
		$dbobject->create('Points',$score);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		}
		return false;		
	}
	
	public static function deleteScore($scoreid){
		$dbobject = new dbobject('aaa_scores');
		$dbobject->destroy();
		$dbobject->where("PK_ScoreID",$scoreid);
		return $dbobject->commit();			
	}
}
?>