<?php

class textmeta{
	/**
     * This method creates a text meta data entry.
	 *
	 * @param integer $textid Text id.
	 * @param integer $speech
	 * @param integer $mail
	 * @param integer $pdf
	 * @param integer $print
	 *
	 * @return integer/bool id on success or false on failure.	 
	 *
	 * @access private
	 * @since Method available since Release 05-10-2014
     */	
	private static function createMetaData($textid, $speech = 0,$mail = 0,$pdf = 0,$print = 0){
		$dbobject = new dbobject('cms_text_meta');
		$dbobject->create('Speech',$speech);
		$dbobject->create('Mail',$mail);
		$dbobject->create('PDF',$pdf);
		$dbobject->create('Print',$print);
		$dbobject->create('FK_TextID',$textid);
		if ($dbobject->commit()){
			return $dbobject->readLastEntry();
		} 
		return false;
	}
	
	/**
	 * Checks if an entry exists.
	 *
	 * @param integer $key
	 *
	 * @return integer/bool id on success or false on failure.	 
	 *
	 * @access private
	 * @since Method available since Release 12-10-2014
     */
	private static function doesExist($key){
		$dbobject = new dbobject('cms_text_meta');
		$dbobject->read("FK_TextID");
		$dbobject->where("FK_TextID",$key);
		list($id) =  $dbobject->fetchSingle();	
		if ($id){
			return $id;
		} else {
			return false;
		}
	}	

	/**
     * This method reads the meta data entry.
	 *
	 * @param integer $textid Text id.
	 *
	 * @return mixed array on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 05-10-2014
     */		
	public static function readMetaData($textid){
		$dbobject = new dbobject('cms_text_meta');
		$dbobject->read("Speech");
		$dbobject->read("Mail");
		$dbobject->read("PDF");
		$dbobject->read("Print");
		$dbobject->where("FK_TextID",$textid);
		$result = $dbobject->fetchSingle();
		return $result;		
	}
	
	/**
     * This method updates a text meta data entry.
	 *
	 * @param integer $textid Text id.
	 * @param integer $speech
	 * @param integer $mail
	 * @param integer $pdf
	 * @param integer $print
	 *
	 * @return integer/bool id on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 05-10-2014
     */		
	public static function updateMetaData($textid, $speech, $mail, $pdf, $print){
		$speech = ($speech) ? 1 : 0;
		$mail = ($mail) ? 1 : 0;
		$pdf = ($pdf) ? 1 : 0;
		$print = ($print) ? 1 : 0;
		if (self::doesExist($textid)){
			$dbobject = new dbobject('cms_text_meta');
			$dbobject->update('Speech',$speech);
			$dbobject->update('Mail',$mail);
			$dbobject->update('PDF',$pdf);
			$dbobject->update('Print',$print);
			$dbobject->where("FK_TextID",$textid);
			return $dbobject->commit();	
		} else {
			return self::createMetaData($textid,$speech,$mail,$pdf,$print);
		}		
	}
	
	/**
     * This method deletes all meta data relating to the textid
	 *
	 * @param integer $textid The foreign key to the table.
	 *
	 * @return bool True on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 06-10-2014	
     */		
	public static function destroyMetaData($textid){
		$dbobject = new dbobject('cms_text_meta');
		$dbobject->destroy();
		$dbobject->where("FK_TextID",$textid);
		return $dbobject->commit();		
	}	
}
?>