<?php
class questionnaire{

	public static function createPoll($key){
		$questionlist = '';
		list($pollid,$headline,$description,$online,$offline,$markers) = poll::readPollOnKey($key);
		$groups = questiongroup::readGroupOnPollID($pollid);

		if (is_array($groups)){
			
			$questionlist .= '<div><h4>'.$headline.'</h4>';
			$questionlist .= $description.'</div>';
			
			foreach($groups as $group){
				list($groupid,$groupname) = $group;
				$questions = question::listUserQuestions($groupid);
				if ($questions){
					$questionlist .= '<table cellspacing="0" cellpadding="1" border="1" class="questiontable col-xs-12">';
					$questionlist .= '<tr>';
					if ($markers){
						$questionlist .= '<td><h4 style="min-width:60%">'.$groupname.'</h4></td>';
						list($min, $max) = questiongroup::readMinMaxOnGroup($groupid);
						for ($i = $min; $i < $max + 1; $i++){
							$questionlist .= '<td style="text-align:center;width:25px">'.$i.'</td>';
						}
					} else {
						$questionlist .= '<td colspan="100%"><h4>'.$groupname.'</h4></td>';
					}
					$questionlist .= '</tr>';
					foreach($questions as $singlequestion){
						list($questionid,$question,$min,$max,$answer) = $singlequestion;
						$questionlist .= question::createPollRadio('question'.$questionid,$question,$min,$max,$answer);
					}
					$questionlist .= form::input($key,'pollname',HIDDEN);
					$questionlist .= '</table>';
				}
			}
		} else {
			$questionlist = '{'.$key.'}';
		}
		return $questionlist;
	}
}
?>