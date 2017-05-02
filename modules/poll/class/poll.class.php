<?php

class poll{

	public static function listPollsDropDown(){
		$dbobject = new dbobject("poll");
		$dbobject->read("PK_PollID");
		$dbobject->read("Headline");	
		$dbobject->where("Language",language::get());
		$dbobject->orderby("Headline");
		return $dbobject->fetch();			
	}

	public static function listPolls($searchval = ''){
		$dbobject = new dbobject("poll");
		$dbobject->read("PK_PollID");
		$dbobject->read("Headline");	
		$dbobject->read("DateOnline");	
		$dbobject->read("DateOffline");	
		$dbobject->read("PollKey");	
		if ($searchval){
			$dbobject->wildcard("Question",$searchval);
			$dbobject->wildcard("GroupName",$searchval);		
		}		
		$dbobject->where("Language",language::get());
		return $dbobject->fetch();			
	}
	
	public static function readPoll($pollid){
		$dbobject = new dbobject("poll");
		$dbobject->read("PK_PollID");
		$dbobject->read("Headline");
		$dbobject->read("Description");
		$dbobject->read("DateOnline");	
		$dbobject->read("DateOffline");	
		$dbobject->read("PollKey");	
		$dbobject->read("UseMarkers");	
		$dbobject->where("PK_PollID",$pollid);
		return $dbobject->fetchSingle();
	}
	
	public static function readPollOnKey($key){
		$dbobject = new dbobject("poll");
		$dbobject->read("PK_PollID");
		$dbobject->read("Headline");
		$dbobject->read("Description");
		$dbobject->read("DateOnline");	
		$dbobject->read("DateOffline");	
		$dbobject->read("UseMarkers");	
		$dbobject->where("PollKey",$key);
		return $dbobject->fetchSingle();
	}
	
	public static function createPoll($headline, $description, $dateonline, $dateoffline, $key, $marker){
		$dbobject = new dbobject("poll");
		$dbobject->create('Headline',$headline);
		$dbobject->create('Description',$description);
		$dbobject->create('DateOnline',$dateonline);	
		$dbobject->create('DateOffline',$dateoffline);	
		$dbobject->create('PollKey',$key);	
		$dbobject->create('UseMarkers',$marker);	
		$dbobject->create('Language',language::get());	
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		} 
		return false;	
	}

	public static function updatePoll($pollid, $headline, $description, $dateonline, $dateoffline, $key, $marker){
		$dbobject = new dbobject("poll");
		$dbobject->update('Headline',$headline);
		$dbobject->update('Description',$description);
		$dbobject->update('DateOnline',$dateonline);
		$dbobject->update('DateOffline',$dateoffline);
		$dbobject->update('PollKey',$key);
		$dbobject->update('UseMarkers',$marker);
		$dbobject->where("PK_PollID",$pollid);
		return $dbobject->commit();	
	}
	
	public static function destroyPoll($pollid){
		$dbobject = new dbobject("poll");
		$dbobject->destroy();
		$dbobject->where("PK_PollID",$pollid);
		return $dbobject->commit();			
	}
}

?>