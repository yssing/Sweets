<?php
/**
 * This class handles database data fetching. It will return data objects with data fetched from
 * a given table.
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
 * @package		database
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 * @require		'generic.io.class.php'
 */

class dbobject extends genericIO{

	/**
     * The databaseObject
	 *
     * @access public
     */	
	private $databaseHandler = '';
	
	/**
	 * the name of the table to access
	 *
	 * @access public
	 */
	private $dbobject_table = ''; 
	
	/**
	 * the sql string
	 *
	 * @access public
	 */
	private $dbobject_query = '';
	
	private $dbobject_where = '';
	
	private $dbobject_read = '';
	
	private $dbobject_order = '';
	
	private $dbobject_limit = '';
	
	private $dbobject_type = '';
	
	private $dbobject_delete = '';
	
	private $dbobject_update = '';
	
	private $dbobject_join = '';
	
	private $dbobject_create = array();

	/**
     * The variable is used to tell methods if a transaction is already in progress
	 *
     * @access private
     */		
	private $in_transaction = false;	
	
	public function __construct($table = ''){
		$this->dbobject_table = TPREP.$table;
		try {
			$this->databaseHandler = new PDO('mysql:host='.DATABASESERVER.';dbname='.DATABASE, DBUSER, DBPASSWORD, array(
				PDO::ATTR_PERSISTENT => true));

		} catch (PDOException $e) {
			self::DBug('Could not connect: '.$e->getMessage());
			die();
		}
	}
	
	/**
	 * Starts transaction
	 *
	 * @return boolean If transaction was started successfully
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function TransactionBegin() {
		if ($this->in_transaction) {
			return false;
		}		
		try{
			$prepared = $this->databaseHandler->prepare("START TRANSACTION");
			if (!$prepared->execute()) {
				throw new Exception('It was not possible to START TRANSACTION');
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__);
			return false;
		}		
		$this->in_transaction = true;
		return true;
	}

	/**
	 * Ends/commits transaction
	 *
	 * @return boolean If committing was successful
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function TransactionEnd() {
		if (!$this->in_transaction) {
			return false;
		}
		try{
			$prepared = $this->databaseHandler->prepare("COMMIT");
			if (!$prepared->execute()) {
				throw new Exception('It was not possible to COMMIT TRANSACTION');
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__);
			return false;
		}
		return true;
	}

	/**
	 * Rolls back current transaction
	 *
	 * @return boolean If rolling back was successful
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function TransactionRollback() {
		if (!$this->in_transaction) {
			return false;
		}
		try{
			$prepared = $this->databaseHandler->prepare("ROLLBACK");
			if (!$prepared->execute()) {
				throw new Exception('It was not possible to ROLLBACK TRANSACTION');
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__);
			return false;
		}		
		$this->in_transaction = false;
		return true;
	}	

	/**
	 * Locks a table.
	 *
	 * As standard this method locks a table for writing, this can be usefull
	 * where data integrity is important.
	 *
	 * @param array $string what kind of lock.	 
	 *
	 * @return boolean If lock was successful
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public function lock($type = "WRITE"){
		try{
			$sql = "LOCK TABLES ".$this->dbobject_table." ".$type;
			$prepared = $this->databaseHandler->prepare($sql);
			if (!$prepared->execute()) {
				throw new Exception('Could not lock '.TPREP.$table);
			}
		}
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			return false;
		}

		return true;
	}
	
	/**
	 * Unlocks a table.
	 *
	 * As standard this method locks a table for writing, this can be usefull
	 * where data integrity is important.	 
	 *
	 * @return boolean If lock was successful
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public function unlock(){
		try{
			$sql = "UNLOCK TABLES";
			$prepared = $this->databaseHandler->prepare($sql);
			if (!$prepared->execute()) {
				throw new Exception('Could not unlock tables');
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			return false;
		}
		return true;	
	}	
	
	/**
     * This method disables a row in the table.
	 * It does this simply, by changing the DisabledDate to the current time.
	 * The method uses the getPrivateKey to find the private key for the table, 
	 * so its not necessary to provide it with the tables private key
	 *
	 * @param int $id The id for the row in the table.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function disable($id){
		self::where(self::getPrivateKey(),$id);
		self::update("DisabledDate","NOW()");
	}

	/**
     * This method enables a row in the table.
	 * It simply sets the DisabledDate to 0
	 * The methods uses the same approach as the disable method
	 *
	 * @param int $id The id for the row in the table.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function enable($id){
		self::where(self::getPrivateKey(),$id);
		self::update("DisabledDate","'0000-00-00 00:00:00'");		
	}		

	/**
     * This function counts the numbers of columns in a table.
	 * 
	 * This function can count the number of rows in a table.
	 * The information is fetched from MySQL information_schema
	 *
	 * @return int Returns the number of table rows on success or 0 on failure.
	 *
     * @access private
	 * @since Method available since Release 1.0.0
     */		
	private function countAffectedColumns(){
		$data = 0;
		try{
			$sql = "SELECT count(*) AS counter FROM information_schema.`COLUMNS` C";
			$sql .= " WHERE table_name = '".$this->dbobject_table."'";
			$sql .= " AND TABLE_SCHEMA = '".DATABASE."'";
		
			$prepared = $this->databaseHandler->prepare($sql);
			if ($prepared->execute()) {
				while ($row = $prepared->fetch()) {
					$data = $row['counter'];
				}
			} else {
				throw new Exception('Could not count number of columns in table: '.$this->dbobject_table);
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
		}
		return $data;
	}
	
	/**
     * This function finds the private key of a table.
	 *
	 * @return string Returns the name of the row on success or '' on failure.
	 *
     * @access private
	 * @since Method available since Release 1.0.0
     */
	private function getPrivateKey(){
		$data = '';
		try{
			$sql = "SELECT COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE K";
			$sql .= " WHERE TABLE_NAME = '".$this->dbobject_table."'";
			$sql .= " AND TABLE_SCHEMA = '".DATABASE."'";
		
			$prepared = $this->databaseHandler->prepare($sql);
			if ($prepared->execute()) {
				while ($row = $prepared->fetch()) {
					$data = $row['COLUMN_NAME'];
				}
			} else {
				throw new Exception('Could not find private key in table: '.$this->dbobject_table);
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
		}		
		return $data;
	}	
	
	public function table($table){
		$this->dbobject_table = $table;
	}
	
	public function where($key,$value){
		if ($this->dbobject_where){
			$this->dbobject_where .= ' AND '.$key.'='.$value;
		} else {
			$this->dbobject_where = $key.'='.$value;
		}
	}
	
	public function distinct($distinct, $as = ''){
		$as = ($as) ? ' AS '.$as : ''; 
		if ($this->dbobject_select){
			$this->dbobject_select .= ', DISTINCT('.$select.') '.$as;
		} else {
			$this->dbobject_select = ' DISTINCT('.$select.') '.$as;	
		}		
	}
	
	public function limit($from,$to = ''){
		$this->dbobject_limit = 'LIMIT '.$from;
		if($to){
			$this->dbobject_limit .= ' ,'.$to;
		}
	}
	
	public function disabled($disabled){
		self::where('disabled','0000-00-00 00:00:00');
	}
	
	public function orderby($orderby,$order = ''){
		if ($this->dbobject_order){
			$this->dbobject_order .= ', '.$orderby.' '.$order;
		} else {
			$this->dbobject_order = $orderby.' '.$order;
		}
	}
	
	public function join($table, $primarykey, $secondarykey, $join = 'FULL JOIN'){
		$this->dbobject_join = ' '.$join.' '.$table.' ON '.$primarykey.'='.$secondarykey;	
	}
	
	public function create($key,$value){
		$this->dbobject_type = 'create';
		if(!$this->dbobject_create){
			$this->dbobject_create = array();
		}
		$this->dbobject_create[] = array($key,$value);
	}

	public function read($select, $as = ''){
		$this->dbobject_type = 'read';
		$as = ($as) ? ' AS '.$as : ''; 
		if ($this->dbobject_read){
			$this->dbobject_read .= ', '.$select.$as;
		} else {
			$this->dbobject_read = $select.$as;
		}
	}	
		
	public function update($key,$value){
		$this->dbobject_type = 'update';		
		if ($this->dbobject_update){
			$this->dbobject_update .= ', '.$key.'='.$value;
		} else {
			$this->dbobject_update = $key.'='.$value;
		}		
		
	}
	
	public function destroy(){
		$this->dbobject_type = 'destroy';
		$this->dbobject_delete = 'DELETE FROM ';
	}	
	
	public function commit(){
	
		switch($this->dbobject_type){
			case 'select': 
			
			break;
			case 'insert':
			
			break;
			case 'update':
				$this->dbobject_query = 'UPDATE '.$this->dbobject_table.' SET '.$this->dbobject_update.' '.$this->dbobject_where;
			break;
			case 'delete':
				$this->dbobject_query = $this->dbobject_delete.' '.$this->dbobject_table.' '.$this->dbobject_where;
			break;
		}
	
		try{
			self::TransactionBegin();
			
			$prepared = $this->databaseHandler->prepare($this->dbobject_query);
			if (!$prepared->execute()) {
				throw new Exception('Could not commit Query');
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$this->dbobject_query);
			self::TransactionRollback();
			return false;
		}	
		self::TransactionEnd();
		return true;
	}
	
	public function returnSQL(){
		return $this->dbobject_query;
	}
	
	public function __destruct(){
	
	}
}