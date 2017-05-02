<?php
/**
 * This class handles database database access.
 * It uses PDO and the PDO bindParam to avoid SQL injection
 * It will use memcache if available.
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

include_once('baseclass.class.php');
include_once('caching.class.php');
class dbobject extends baseclass{

	/**
	 * The databaseObject
	 *
	 * @access protected
	 */
	protected $databaseHandler = '';
	
	/**
	 * the name of the table to access
	 *
	 * @access protected
	 */
	protected $dbobject_table = ''; 
	
	/**
	 * the sql string
	 *
	 * @access protected
	 */
	protected $dbobject_query = '';
	
	protected $dbobject_where = '';
	
	protected $dbobject_c_where = '';
	
	protected $dbobject_read = '';
	
	protected $dbobject_order = '';
	
	protected $dbobject_limit = '';
	
	protected $dbobject_type = '';
	
	protected $dbobject_delete = '';
	
	protected $dbobject_update = '';
	
	protected $dbobject_join = '';
	
	protected $dbobject_shortname = '';
	
	protected $dbobject_collate = '';
	
	protected $dbobject_create = array();
	
	protected $dbobject_create_key = array();
	
	protected $dbobject_create_val = array();
	
	protected $bind_array = array();

	/**
	 * The variable is used to tell methods if a transaction is already in progress
	 *
	 * @access private
	 */
	protected $in_transaction = false;	
	
	public function __construct($table = '', $shortname = ''){
		parent::__construct();
		
		if ($shortname){
			$this->dbobject_shortname = TPREP.$shortname;
		}
		
		$this->dbobject_table = TPREP.$table;
		try {	
			$this->databaseHandler = new PDO('mysql:host='.DATABASESERVER.';dbname='.DATABASE, DBUSER, DBPASSWORD, array(
				PDO::ATTR_PERSISTENT => true, 
				PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));	
		} catch (PDOException $e) {
			self::DBug('Could not connect: '.$e->getMessage());
			exit();
		}
	}

	public function short($shortname = ''){
		$this->dbobject_shortname = TPREP.$shortname;
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
	 * As standard this method locks a table for writing, this can be useful
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
	 * @return boolean If unlock was successful
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

	public function doesDBExist($database){	
		$data = '';
		try{
			$sql = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".$database."'";
			
			$tmpData = caching::getKey(md5($sql));

			if (!$tmpData){
				$prepared = $this->databaseHandler->prepare($sql);
				foreach ($this->bind_array as $key) {
					$prepared->bindParam($key[0], $key[1]);
				}

				if ($prepared->execute()) {
					while ($row = $prepared->fetch()) {
						$data = $row['SCHEMA_NAME'];
					}
					caching::setKey(md5($sql),$data);
				} else {
					throw new Exception('Could not determine by INFORMATION_SCHEMA if '.$database.' exists');
				}
			} else {
				$data = $tmpData;
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			return false;
		}
		return $data;
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
			$this->dbobject_query = "SELECT count(*) AS counter FROM information_schema.`COLUMNS` C";
			$this->dbobject_query .= " WHERE table_name = '".$this->dbobject_table."'";
			$this->dbobject_query .= " AND TABLE_SCHEMA = '".DATABASE."'";
		
			$data = self::fetchSingle('FETCH_ASSOC');
			$data = $data['counter'];
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$this->dbobject_query);
		}
		return $data;
	}

	/**
	 * This method counts rows all the rows in a given table.
	 *
	 * @param string $table the table to use from the database.
	 *
	 * @return int/bool Rows on success or FALSE on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function count(){
		$data = 0;
		try{
			$this->dbobject_query = "SELECT count(".self::getPrivateKey().") AS counter FROM ".$this->dbobject_table." ";
			if ($this->dbobject_where){
				$this->dbobject_query .= $this->dbobject_where;
			}
			$data = self::fetchSingle('FETCH_ASSOC');
			$data = $data['counter'];

			if ($data !== false) {
				return $data;
			} else {
				throw new Exception('Could not count number of columns in table: '.$this->db_table);
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$this->dbobject_query);
		}
		return false;
	}
	
	/**
	 * This method finds the private key of a table.
	 *
	 * @return string Returns the name of the row on success or '' on failure.
	 *
	 * @access private
	 * @since Method available since Release 1.0.0
	 */
	protected function getPrivateKey(){
		$data = '';
		try{
		
			$this->dbobject_query = "SELECT COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE K";
			$this->dbobject_query .= " WHERE TABLE_NAME = '".$this->dbobject_table."'";
			$this->dbobject_query .= " AND TABLE_SCHEMA = '".DATABASE."'";
			
			$data = self::fetchSingle('FETCH_ASSOC');
			$data = $data['COLUMN_NAME'];

		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
		}		
		return $data;
	}	

	/**
	 * This method is used to change the table set with the constructor.
	 *
	 * @param string $table	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function table($table){

		if ($this->dbobject_table){
			$this->dbobject_table .= ', '.TPREP.$table;
		} else {
			$this->dbobject_table = TPREP.$table;
		}
	}
	
	/**
	 * This method is used to set collation in order to distinct between upper and lower cased chars
	 *
	 * @param string $coltype defaults to utf8_bin
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function collate($coltype = 'utf8_bin'){
		$this->dbobject_collate = 'COLLATE '.$coltype;
	}
	
	/**
	 * This method is used to set where clauses.
	 *
	 * It will prepend the value with the name of the table from the constructor, only if a table name have not been given with the key.
	 *
	 * @param string $key
	 * @param string $value 
	 * @param string $type type of comparator, defaults to =
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function where($key,$value,$type = '='){
		
		$table = $this->dbobject_table;	
		if($this->dbobject_shortname){
			$table = $this->dbobject_shortname;
		}		
		
		if (strpos($key,'.') === false){
			if ($this->dbobject_where){
				$this->dbobject_where .= ' AND '.$table.'.'.$key.' '.$type.' :'.strtoupper($key);
				$this->dbobject_c_where .= ' AND '.$table.'.'.$key.' '.$type.' '.$value;
			} else {
				$this->dbobject_where = ' WHERE '.$table.'.'.$key.' '.$type.' :'.strtoupper($key);
				$this->dbobject_c_where = ' WHERE '.$table.'.'.$key.' '.$type.' '.$value;
			}
			$this->bind_array[] = array(':'.strtoupper($key),$value);
		} else {
			$keyshort = str_replace(".","",$key);
			if ($this->dbobject_where){
				$this->dbobject_where .= ' AND '.TPREP.$key.' '.$type.' :'.strtoupper($keyshort);
				$this->dbobject_c_where .= ' AND '.TPREP.$key.' '.$type.' '.$value;
			} else {
				$this->dbobject_where = ' WHERE '.TPREP.$key.' '.$type.' :'.strtoupper($keyshort);
				$this->dbobject_c_where = ' WHERE '.TPREP.$key.' '.$type.' '.$value;
			}
			$this->bind_array[] = array(':'.strtoupper($keyshort),$value);
		}
	}
	
	/**
	 * This method is used to create where clauses to select values between min or max values.
	 * If only either min og max value is found, then the method will setup the appropriate
	 * search clauses for either of these values. This way the method can be used for min or max
	 * comparisons on number values.
	 * Both minval and maxval defaults to 0
	 *
	 * @param string $key
	 * @param integer $minval 
	 * @param integer $maxval 
	 *
	 * @access public
	 * @since Method available since Release 16-12-2015
	 */	
	public function between($key,$minval = 0,$maxval = 0){

		$andwhere = ($this->dbobject_where) ? ' AND ' : ' WHERE '; 
		if (strpos($key,'.') === false){
			$searchkey = $this->dbobject_table.'.'.$key;
		} else {
			$searchkey = TPREP.$key;
		}

		if($minval > 0 && $maxval == 0){
			$this->dbobject_where .= $andwhere.' :MINVAL < '.$searchkey;
			$this->bind_array[] = array(':MINVAL',$minval);
		} else if ($minval == 0 && $maxval > 0) {
			$this->dbobject_where .= $andwhere.' :MAXVAL > '.$searchkey;
			$this->bind_array[] = array(':MAXVAL',$maxval);
		} else {		
			$this->dbobject_where .= $andwhere.' (:MINVAL < '.$searchkey.' AND '.$searchkey.' < :MAXVAL)';
			$this->dbobject_c_where .= $andwhere.' ('.$minval.' < '.$searchkey.' AND '.$searchkey.' < '.$maxval.')';
			$this->bind_array[] = array(':MINVAL',$minval);
			$this->bind_array[] = array(':MAXVAL',$maxval);
		}
	}
	
	/**
	 * This method is used to serach for similar text patterns.
	 * This is usefull on searches, where spelling might not be entirely correct
	 *
	 * @param string $key
	 * @param string $value
	 * @param string $type where combination, defaults to AND
	 *
	 * @access public
	 * @since Method available since Release 22-12-2015
	 */	
	public function similarTo($key,$value,$type = 'AND'){
		if ($this->dbobject_where){
			$this->dbobject_where .= ' ' . $type . ' SOUNDEX('.$key.') = SOUNDEX(:'.strtoupper($key).')';
		} else {
			$this->dbobject_where = ' WHERE SOUNDEX('.$key.') = SOUNDEX(:'.strtoupper($key).')';
		}

		$this->bind_array[] = array(':'.strtoupper($key),$value);
	}
	
	/**
	 * This method is used to set distinctions.
	 *
	 * @param string $distinct
	 * @param string $as
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function distinct($distinct, $as = ''){
		$this->dbobject_type = 'read';
		$as = ($as) ? ' AS '.$as : ''; 
		if ($this->dbobject_read){
			$this->dbobject_read .= ', DISTINCT('.$distinct.')'.$as;
		} else {
			$this->dbobject_read = ' SELECT DISTINCT('.$distinct.')'.$as;
		}
	}
	
	/**
	 * This method is used to set limit.
	 * If it is given a second value, then the offset, beginning, is
	 * is the first variable and the second becomes the limit.
	 *
	 * @param int $limit
	 * @param int $offset
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function limit($limit,$offset = 0){
	
		if ($offset){
			$this->dbobject_limit = 'LIMIT '.$offset;
			//if (DATABASETYPE == 'SQLITE'){
				$this->dbobject_limit .= ' OFFSET '.$limit;
			//} else {
			//	$this->dbobject_limit .= ' ,'.$limit;
			//}
		} else {
			$this->dbobject_limit = 'LIMIT '.$limit;
		}
	}
	
	/**
	 * This method is a shorthand for setting disabled as a where clause
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function disabled(){
		self::where('disabled','0000-00-00 00:00:00');
	}
	
	/**
	 * This method is used to orderby
	 *
	 * @param string $orderby
	 * @param string $order
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function orderby($orderby,$order = ''){
		if ($this->dbobject_order){
			$this->dbobject_order .= ', '.$orderby.' '.$order;
		} else {
			$this->dbobject_order = ' ORDER BY '. $orderby.' '.$order;
		}
	}

	/**
	 * This method is used to join two tables.
	 * it uses LEFT JOIN as default join method
	 *
	 * @param string $foreignTable
	 * @param string $foreignTableKey 
	 * @param string $key
	 * @param string $join 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function join($foreignTable, $foreignTableKey, $key, $join = 'LEFT JOIN'){
		$table = $this->dbobject_table;	
		if($this->dbobject_shortname){
			$table = $this->dbobject_shortname;
		}
		
		$this->dbobject_join .= ' '.$join.' '.TPREP.$foreignTable.' ON '.$table.'.'.$key . ' = ' . TPREP.$foreignTable.'.'.$foreignTableKey;
	}
	
	/**
	 * This method can be used to search with wild cards
	 * If multiple where statements are used, then it will add an OR
	 *
	 * @param string $key
	 * @param string $value
	 * @param string $type Is used to determine how the wild card search is done.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function wildcard($key,$value,$type = 1){
		if ($this->dbobject_where){
			$this->dbobject_where .= ' OR '.$key.' LIKE :'.strtoupper($key);
		} else {
			$this->dbobject_where = ' WHERE '.$key.' LIKE :'.strtoupper($key);
		}
		switch($type){
			case 1:
				$this->bind_array[] = array(':'.strtoupper($key),"%".$value."%");
			break;
			case 2:
				$this->bind_array[] = array(':'.strtoupper($key),"%".$value);
			break;
			case 3:
				$this->bind_array[] = array(':'.strtoupper($key),$value."%");
			break;
		}
	}
	
	/**
	 * This method is used to create a new entry in the database.
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function create($key,$value){
		$this->dbobject_type = 'create';
		if (!$this->dbobject_create){
			$this->dbobject_create = array();
		}
		$this->dbobject_create_key[] = $key;
		$this->dbobject_create_val[] = ':'.strtoupper($key);
		
		$this->bind_array[] = array(':'.strtoupper($key),self::escapeChars($value));
	}

	/**
	 * This method is used to read a given column in the table.
	 *
	 * @param string $select
	 * @param string $as
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function read($select, $as = ''){
		$this->dbobject_type = 'read';
		$as = ($as) ? ' AS '.$as : ''; 
		if ($this->dbobject_read){
			$this->dbobject_read .= ', '.$select.$as;
		} else {
			$this->dbobject_read = ' SELECT '.$select.$as;
		}
	}	
	
	/**
	 * This method is used to update an entry in the table.
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function update($key,$value){
		$this->dbobject_type = 'update';
		if ($this->dbobject_update){
			$this->dbobject_update .= ', '.$key. '= :'.strtoupper($key);
		} else {
			$this->dbobject_update = $key.' = :'.strtoupper($key);
		}	
		$this->bind_array[] = array(':'.strtoupper($key),self::escapeChars($value));
	}
	
	/**
	 * This method is used to delete an entry in the table.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function destroy(){
		$this->dbobject_type = 'destroy';
		$this->dbobject_delete = 'DELETE FROM ';
	}
	
	/**
	 * This method returns the SQL string.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function returnSQL(){
		return $this->dbobject_query.'<br />';
	}

	/**
	 * This function finds the value of the last created private key.
	 *
	 * The method will look for the tables private key and use that
	 *
	 * @return bool/int Returns integer of PrivateKey on success or FALSE on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function readLastEntry(){
		$PK = self::getPrivateKey($this->dbobject_table);
		$data = array();
		try{
			$this->dbobject_query = "SELECT ".$PK." FROM ".$this->dbobject_table." ";
			if ($this->dbobject_where){
				$this->dbobject_query .= $this->dbobject_where;
			}
			$this->dbobject_query .= " ORDER BY ".$PK." DESC LIMIT 1";

			$data = self::fetchSingle('FETCH_ASSOC');
			return $data[$PK];
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$this->dbobject_query);
			return false;
		}
		return false;
	}

	/**
     * This method is used to sum numbers.
	 *
	 * @param string $distinct
	 * @param string $as	 	 
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function sum($distinct, $as = ''){
		$this->dbobject_type = 'read';
		$as = ($as) ? ' AS '.$as : ''; 
		if ($this->dbobject_read){
			$this->dbobject_read .= ', SUM('.$distinct.')'.$as;
		} else {
			$this->dbobject_read = ' SELECT SUM('.$distinct.')'.$as;
		}		
	}	
	
	/**
	 * This method will build a create query based on the arrays set in the create method
	 *
	 * @return string the formatted create query.
	 *
	 * @access private
	 * @since Method available since Release 1.0.0
	 */
	private function makeCreate(){
		$key = array();
		$keyData = array();

		$arraylength = sizeof($this->dbobject_create_key);
		
		$key = $this->dbobject_create_key;
		$keyData = $this->dbobject_create_val;

		$sql = "INSERT INTO ".$this->dbobject_table." (";
		for($i=0; $i<$arraylength; $i++){
			$sql .= trim($key[$i]);
			if ($i < $arraylength-1){
				$sql .= ",";
			}
		}
		$sql .= ",CreateDate,FK_UserID";
		$sql .= ") VALUES (";

		for($i=0; $i<$arraylength; $i++){
			$sql .= trim($keyData[$i]);
			if ($i < $arraylength-1){
				$sql .= ",";
			}
		}
		
		if (self::$adminid){
			$tmpuser = self::$adminid;
		} else {
			$tmpuser = self::$userid;
		}
		
		$sql .= ", NOW(), ".$tmpuser;
		$sql .= ")";
		return $sql;
	}
	
	/**
	 * This method is a simple shorthand method of the fetch method, it unpacks the array from
	 * fetch() and return the first entry as the final result.
	 *
	 * @param string $method The PDO fetch method, defaults to FETCH_NUM	 
	 *
	 * @return array the row with data.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function fetchSingle($method = 'FETCH_NUM'){
		if ($row = self::fetch($method)){
			return $row[0];
		}
		return false;
	}
	
	/**
	 * This method fetches data from the table, it will return the data as a 2D array.
	 *
	 * @param string $method The PDO fetch method, defaults to FETCH_NUM
	 *
	 * @return array the row with data.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function fetch($method = 'FETCH_NUM'){
		$data = '';
		$tmpData = '';
		$table = '';
		try{			
		
			$table = $this->dbobject_table;	
			if($this->dbobject_shortname){
				$table = $table . ' ' . $this->dbobject_shortname;
			}
			
			if (!$this->dbobject_query){
				$this->dbobject_read = ($this->dbobject_read) ? $this->dbobject_read : 'SELECT *';
				$this->dbobject_query = trim($this->dbobject_read.' FROM '.$table.' '.$this->dbobject_join.' '.$this->dbobject_where.' '.$this->dbobject_collate.' '.$this->dbobject_order.' '.$this->dbobject_limit);
				$cache_query = trim($this->dbobject_read.' FROM '.$table.' '.$this->dbobject_join.' '.$this->dbobject_c_where.' '.$this->dbobject_order.' '.$this->dbobject_limit);
			} else {
				$cache_query = $this->dbobject_query;
			}
			
			if (!baseclass::$adminid){
				$tmpData = caching::getKey(md5($cache_query));
			} 
			if (!$tmpData){
				$prepared = $this->databaseHandler->prepare($this->dbobject_query);
				foreach ($this->bind_array as $key) {
					$prepared->bindParam($key[0], $key[1]);
				}
				
				if ($prepared->execute()) {
					switch($method){
						case 'FETCH_NUM':
							while ($row = $prepared->fetch(PDO::FETCH_NUM)) {
								$data[] = $row;
							}
							break;
						case 'FETCH_ASSOC':
							while ($row = $prepared->fetch(PDO::FETCH_ASSOC)) {
								$data[] = $row;
							}
							break;	
						case 'FETCH_OBJ':
							while ($row = $prepared->fetch(PDO::FETCH_OBJ)) {
								$data[] = $row;
							}
							break;
					}
					caching::setKey(md5($cache_query),$data);
				} else {
					throw new Exception('Could not fetch data from table: '.$this->dbobject_table);
				}
			} else {
				$data = $tmpData;
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$this->dbobject_query);
			return false;
		}
		return $data;
	}

	/**
	 * This method commits any changes made.
	 *
	 * @return bool true on success false on failure.
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
	 */
	public function commit(){	
		switch($this->dbobject_type){
			case 'create':
				$this->dbobject_query = self::makeCreate();
			break;
			case 'update':
				$this->dbobject_query = 'UPDATE '.$this->dbobject_table.' SET '.$this->dbobject_update.' '.$this->dbobject_where;
			break;
			case 'destroy':
				$this->dbobject_query = $this->dbobject_delete.' '.$this->dbobject_table.' '.$this->dbobject_where;
			break;
		}
	
		try{
			self::TransactionBegin();
			
			$prepared = $this->databaseHandler->prepare($this->dbobject_query);
			
			foreach ($this->bind_array as $key) {
				$prepared->bindParam($key[0], $key[1]);
			}
			
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
	
	/**
	 * This method resets variables.
	 *
	 * @access private
	 * @since Method available since Release 1.0.0
	 */
	private function resetQuery(){
		$this->databaseHandler = '';
		$this->dbobject_table = '';
		$this->dbobject_query = '';
		$this->dbobject_where = '';
		$this->dbobject_read = '';
		$this->dbobject_order = '';
		$this->dbobject_limit = '';
		$this->dbobject_type = '';
		$this->dbobject_delete = '';
		$this->dbobject_update = '';
		$this->dbobject_join = '';
		$this->dbobject_create = array();
		$this->dbobject_create_key = array();
		$this->dbobject_create_val = array();
		$this->bind_array = array();
	}

	public function __destruct(){
		self::resetQuery();
	}
}
?>