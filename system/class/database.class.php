<?php
/**
 * This class handles database connections, abstraction layer and caching.
 * Since it uses some defined values, it must include the defines.php
 * Since only classes should access the database, all methods are
 * declared as either public or private.
 * When using an associative array as the "where" clause, the key must correlate
 * to the column name in the database. 
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
require_once('generic.IO.class.php');
class database extends genericIO{
	/**
     * The databaseObject
	 *
     * @access public
     */	
	public $databaseHandler = '';
	
	/**
	 * the name of the table to access
	 *
	 * @access public
	 */
	public $tablename = ''; 
	
	/**
	 * the sql string
	 *
	 * @access public
	 */
	public $sqlString = ''; 	

	/**
     * The variable is used to tell methods if a transaction is already in progress
	 *
     * @access private
     */		
	private $in_transaction = false;
	
	/**
     * The constructor handles initializing of the database object.
	 * Based on the values in the defines
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function __construct(){
		parent::__construct();
		try {
			$this->databaseHandler = new PDO('mysql:host='.DATABASESERVER.';dbname='.DATABASE, DBUSER, DBPASSWORD, array(
				PDO::ATTR_PERSISTENT => true));

		} catch (PDOException $e) {
			self::DBug('Could not connect: '.$e->getMessage());
			die();
		}
	}	
	
	/**
     * This method returns the sql string, that an other method have created.
	 *
	 * @return string the sql string created by a method
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */			
	public function returnSQL(){
		return $this->sqlString;
	}
	
	/**
     * This function shows the databases that the database object has access to
	 * ONLY USE THIS FOR TESTING!!
	 *
	 * @return array $data returns an array of table names.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function showDatabases(){
		$data = array();
		if(genericIO::$adminid){
			$outerLoop = $this->databaseHandler->prepare("SHOW DATABASES");
			if ($outerLoop->execute()) {
				while ($row = $outerLoop->fetch()) {
					$data[] = array($row['Database']);
				}
			}
		}
		return $data;
	}
	
	/**
     * This function shows the tables in a database
	 * ONLY USE THIS AS ADMINISTRATOR!!
	 *
	 * @param string $database use this to list the tables of a specific database.
	 *
	 * @return array $data returns an array of database names.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function showTables($database = ''){
		$data = array();
		if(genericIO::$adminid){
			$sql = 'SHOW TABLES ';
			if($database){
				$sql .= 'FROM '.$database;
			}

			$outerLoop = $this->databaseHandler->prepare($sql);
			if ($outerLoop->execute()) {
				while ($row = $outerLoop->fetch()) {
					if($database){
						$data[] = array($row['Tables_in_'.$database]);
					} else {
						$data[] = array($row['Tables_in_information_schema']);
					}
				}
			}
		}
		return $data;
	}
	
	/**
     * This function shows columns of a given table
	 * ONLY USE THIS AS ADMINISTRATOR!!
	 *
	 * @param string $table use this to list the columns of a specific table.
	 *
	 * @return array $data returns an array of column information.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function showColumns($table){	
		$data = array();
		if(genericIO::$adminid){
			$sql = 'SHOW COLUMNS ';
			if($table){
				$sql .= 'FROM '.$table;
			}

			$outerLoop = $this->databaseHandler->prepare($sql);
			if ($outerLoop->execute()) {
				while ($row = $outerLoop->fetch()) {
					for($i=0;$i<6;$i++){
						$tmp[$i] = $row[$i];
					}					
					$data[] = $tmp;
				}
			}
		}
		return $data;	
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
	 * @param string $table the table to be used database.
	 * @param array $string what kind of lock.	 
	 *
	 * @return boolean If lock was successful
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public function lock($table,$type = "WRITE"){
		try{
			$sql = "LOCK TABLES ".TPREP.$table." ".$type;
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
	 * Add a new column to a table.
	 * This method is in no way capable of figuring out anything, so take care what you input	 
	 *
	 * @param string $table the table to be used database.
	 * @param string $name the name of the new column.	 
	 * @param string $type the datatype of the new column.	 
	 *
	 * @return boolean If alter was successful
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */			
	public function addColumn($table,$name,$type){		
		if(!$table || !$name || !$type){
			return false;
		}
		try{
			self::TransactionBegin();
			$sql = 'ALTER TABLE '.$table.' ADD '.$name.' '.$type.' NOT NULL';
			$prepared = $this->databaseHandler->prepare($sql);
			if (!$prepared->execute()) {
				throw new Exception('Could not unlock tables');
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			self::TransactionRollback();
			return false;
		}	
		self::TransactionEnd();
		return true;			
	}
	
	/**
	 * This method creates a table in the database
	 *
	 * If the user does not have the correct permission, then it will throw an error!
	 * The method will add 'NOT NULL' to any column if not already added.
	 * The method will also add 2 default columns, CreateDate and DisabledDate
	 *
	 * @param string $table the table to be created.
	 * @param string $what what columns and settings to create.
	 * @param string $privatekey the tables private key.
	 * @param string $charset what charset to use, default is utf8.
	 *
	 * @return boolean true on success or false on failure
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public function createTable($table,$what = '',$privatekey = '',$charset= 'utf8'){
		if(!$table || !$what){
			return false;
		}
		try{
			self::TransactionBegin();
			if(!$privatekey){
				$privatekey = 'PK_'.$table.'ID';
			}

			$sql = 'CREATE TABLE IF NOT EXISTS `'.TPREP.$table.'` (
			`'.$privatekey.'` int(10) unsigned NOT NULL AUTO_INCREMENT,';
			
			if($what != ''){
				if(is_array($what)){
					while ($rowData = current($what)) {
						if(stripos($rowData,'NOT NULL')){
							$sql .= '`'.key($what).'` '.$rowData.", ";
						} else {
							$sql .= '`'.key($what).'` '.$rowData." NOT NULL, ";
						}
						next($what);
					}
				} else {
					$sql .= $what;
				}
			}

			$sql .= '`FK_UserID` int(10) unsigned NOT NULL,';
			$sql .= '`CreateDate` datetime NOT NULL,';
			$sql .= '`DisabledDate` datetime NOT NULL,';
			$sql .= '`Language` varchar(2) NOT NULL,';			
			$sql .= ' PRIMARY KEY (`'.$privatekey.'`)
			) ENGINE=InnoDB DEFAULT CHARSET='.$charset.' AUTO_INCREMENT=1;';

			$this->sqlString = $sql;
			
			$innerLoop = $this->databaseHandler->prepare($sql);
			if (!$innerLoop->execute()) {
				throw new Exception('Could not create new table: '.TPREP.$table);
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			self::TransactionRollback();
			return false;
		}	
		self::TransactionEnd();
		return true;
	}
	
	/**
	 * This method drops a table from the database
	 *
	 * If the user does not have the correct permission, then it will throw an error!
	 *
	 * @param string $table the table to be created.
	 *
	 * @return boolean true on success or false on failure
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public function dropTable($table){
		if(!$table){
			return false;
		}	
		try{
			self::TransactionBegin();
			$sql = 'DROP TABLE IF EXISTS `'.TPREP.$table.'`';
			$innerLoop = $this->databaseHandler->prepare($sql);
			if (!$innerLoop->execute()) {
				throw new Exception('Could not drop table: '.TPREP.$table);
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			self::TransactionRollback();
			return false;
		}	
		self::TransactionEnd();
		return true;	
	}
	
	/**
	 * This method changes a text field to UTF8 format
	 *
	 * First of all, don't mess with it if everything works fine.
	 * It's usefull with tables containing names etc in a non utf8 format.
	 * It simply runs through a table changing one row and one column
	 * one step at a time.
	 *
	 * @param string $table the table to be used database.
	 * @param string $what what column name to change.
	 *
	 * @return void
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */	
	public function changeToUTF8($table,$what){
		$key = self::getPrivateKey($table);
		$sql = "SELECT ".$key.", ".$what." FROM ".TPREP.$table;

		try{
			$outerLoop = $this->databaseHandler->prepare($sql);
			if ($outerLoop->execute()) {
				while ($row = $outerLoop->fetch()) {
				
					$newname = utf8_encode($row[$what]);				
					$innerSQL = "UPDATE ".TPREP.$table." SET ".$what." = '".$newname."' WHERE ".$key." = ".$row[$key];
					try{
						$newname = utf8_encode($row[$what]);				
						$innerSQL = "UPDATE ".TPREP.$table." SET ".$what." = '".$newname."' WHERE ".$key." = ".$row[$key];					
						$innerLoop = $this->databaseHandler->prepare($innerSQL);
						if (!$innerLoop->execute()) {
							throw new Exception('Could not update column '.$what.' in '.TPREP.$table.' to utf 8!');
						}
					}	
					catch (Exception $e){
						self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
						return false;
					}
				}
			} else {
				throw new Exception('Could not read the table "'.$table.'" for translation to UTF 8!');
				
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			return false;
		}
		return true;
	}	
	
	/**
     * This function creates a new data entry in the database.
	 * 
	 * This function can create an sql string for inserting data
	 * into the database and create a correct cache for the data.
	 * Take care to encapsulate strings with the ' ' chars
	 *
	 * @param string $table the table to be used database.
	 * @param array $values an array of values and keys to be inserted.
	 *
	 * @return bool Returns TRUE on success or FALSE on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function create($table,$values){
		$key = array();
		$keyData = array();
		try{	
			self::TransactionBegin();
			$arraylength = sizeof($values);
			
			$key = array_keys($values);
			$keyData = array_values($values);
		
			$sql = "INSERT INTO ".TPREP.$table." (";			
			for($i=0; $i<$arraylength; $i++){
				$sql .= trim($key[$i]);
				if($i < $arraylength-1){
					$sql .= ",";
				}
			}
			$sql .= ",CreateDate,FK_UserID";
			$sql .= ") VALUES (";
			
			for($i=0; $i<$arraylength; $i++){
				$sql .= trim($keyData[$i]);
				if($i < $arraylength-1){
					$sql .= ",";
				}
			}
			
			if(self::$adminid){
				$tmpuser = self::$adminid;
			} else {
				$tmpuser = self::$userid;
			}
			
			$sql .= ", NOW(), ".$tmpuser;
			$sql .= ")";

			$this->sqlString = $sql;
			
			$prepared = $this->databaseHandler->prepare($sql);
			$execute = $prepared->execute();
			var_dump($execute);
			if (!$execute ) {
				throw new Exception('It was not possible to create new entry in: '.TPREP.$table);				
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			self::TransactionRollback();
			return false;
		}	
		self::TransactionEnd();
		return true;
	}
	
	/**
     * This function fetches data from a table.
	 * 
	 * This function can create an sql string for fetching data 
	 * from a given table.
	 * If the $what is not given, it will just pick all the columns
	 * If its supplied with a where and order condition, then these will
	 * be added to the resulting sql string.
	 * Any class that needs to access the database needs to go through this class.
	 *
	 * @param string $table the table to use from the database.
	 * @param string $what what data to pick. It can only use a comma separated list
	 * @param string $where the conditions, accepted both as comma separated list or as an ass. array.
	 * @param string $order how to sort the data.
	 * @param string $limit how many rows to pick.
	 * @param bool $disabled show disabled, default to false (no show!).
	 *
	 * @return bool/array Returns array of data on success or FALSE on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */	
	public function read($table,$what = '',$where = '',$order = '',$limit = '',$disabled = false){
		$data = array();
		$tmp = array();
		try{
			$sql = "SELECT ";
			if($what){
				$sql .= $what." ";
			} else {
				$sql .= " * ";
				$columns = self::countAffectedColumns($table);
				if(!$columns){
					self::DBug('Fatal error!!');
				}
			}
			$sql .= " FROM ".TPREP.$table." ";
			if($where){
				if(is_array($where)){
					$sql .= " WHERE ";
					$arraylength = sizeof($where);
					for($i=0; $i<$arraylength; $i++){
						$sql .= trim($where[$i]);
						if($i < $arraylength-1){
							$sql .= " AND ";
						}
					}
				} else {
					$sql .= " WHERE ".$where;
				}
				if(!$disabled){
					$sql .= " AND DisabledDate = '0000-00-00 00:00:00'";
				}
			} else {
				if(!$disabled){
					$sql .= " WHERE DisabledDate = '0000-00-00 00:00:00'";
				}
			}
			if($order){
				$sql .= " ORDER BY ".$order;
			}		
			
			if($limit){
				$sql .= " LIMIT ".$limit;
			}	

			$this->sqlString = $sql;
			
			$prepared = $this->databaseHandler->prepare($sql);
			if ($prepared->execute()) {
				while ($row = $prepared->fetch()) {
					if($what){
						$i = 0;
						$rowlist = explode(',',$what);
						foreach($rowlist as $singlerow){
							$tmp[$i] = $row[$i];
							$i++;
						}
					} else {
						for($i=0;$i<$columns;$i++){
							$tmp[$i] = $row[$i];
						}
					}
					$data[] = $tmp;
				}
			} else {
				throw new Exception('Could not fetch data from table: '.TPREP.$table);
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			return false;
		}
		return $data;
	}
	
	/**
     * This method fetches data from a table, it will then build the result in an associative array.
	 * 
	 * The method have to have "what" clause in a comma separated string
	 *
	 * @param string $table the table to use from the database.
	 * @param string $what what data to pick. It can only use a comma separated list
	 * @param string $where the conditions, accepted both as comma separated list or as an ass. array.
	 * @param string $order how to sort the data.
	 * @param string $limit how many rows to pick.
	 * @param bool $disabled show disabled, default to false (no show!).
	 *
	 * @return bool/array Returns array of data on success or FALSE on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function readAssociative($table,$what = '',$where = '',$order = '',$limit = '',$disabled = false){
		$data = array();
		$tmp = array();
		try{
			$sql = "SELECT ";
			if(!$what){
				return false;
			}
			$sql .= $what." ";

			$sql .= " FROM ".TPREP.$table." ";
			if($where){
				if(is_array($where)){
					$sql .= " WHERE ";
					$arraylength = sizeof($where);
					for($i=0; $i<$arraylength; $i++){
						$sql .= trim($where[$i]);
						if($i < $arraylength-1){
							$sql .= " AND ";
						}
					}
				} else {
					$sql .= " WHERE ".$where;
				}
				if(!$disabled){
					$sql .= " AND DisabledDate = '0000-00-00 00:00:00'";
				}
			} else {
				if(!$disabled){
					$sql .= " WHERE DisabledDate = '0000-00-00 00:00:00'";
				}
			}
			if($order){
				$sql .= " ORDER BY ".$order;
			}		
			
			if($limit){
				$sql .= " LIMIT ".$limit;
			}	

			$this->sqlString = $sql;
			
			$prepared = $this->databaseHandler->prepare($sql);
			if ($prepared->execute()) {
				while ($row = $prepared->fetch()) {
					if($what){
						$i = 0;
						$rowlist = explode(',',$what);
						foreach($rowlist as $singlerow){
							$tmp[$singlerow] = $row[$i];
							$i++;
						}
					}
					$data[] = $tmp;
				}
			} else {
				throw new Exception('Could not fetch data from table: '.TPREP.$table);
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			return false;
		}
		return $data;
	}	

	/**
     * This method fetches a single row from a table, it will then build the result in an associative array.
	 *
	 * This method is a short hand form of the method readAssociative. 
	 *
	 * @param string $table the table to use from the database.
	 * @param string $what what data to pick. It can only use a comma separated list
	 * @param string $where the conditions, accepted both as comma separated list or as an ass. array.
	 * @param string $order how to sort the data.
	 * @param bool $disabled show disabled, default to false (no show!).
	 *
	 * @return bool/array Returns array of data on success or FALSE on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */			
	public function readSingleAssociative($table,$what = '',$where = '',$order = '',$disabled = false){
		return self::readAssociative($table,$what,$where,$order,1,$disabled);
	}
	
	/**
     * This function fetches a single row from a table.
	 * It is a shorthand form of the method read, and it will unpack a 2D array to a 1D array
	 *
	 * @param string $table the table to use from the database.
	 * @param string $what what data to pick. It can only use a comma separated list
	 * @param string $where the conditions.
	 * @param string $order how to sort the data.
	 * @param bool $disabled show disabled, default to false (no show!).
	 *
	 * @return bool/array Returns array of data on success or FALSE on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function readSingle($table,$what = '',$where = '',$order = '',$disabled = false){
		$data = self::read($table,$what,$where,$order,1,$disabled);		
		if(is_array($data) && $data){
			return $data[0];		
		} else {
			return $data;
		}
		/*
		$data = array();
		$tmp = array();
		try{
			$sql = "SELECT ";
			if($what){
				$sql .= $what." ";
			} else {
				$sql .= " * ";
				$columns = self::countAffectedColumns($table);
				if(!$columns){
					self::DBug('Fatal error!!');
				}
			}
			$sql .= " FROM ".TPREP.$table." ";
			if($where){
				if(is_array($where)){
					$sql .= " WHERE ";
					$arraylength = sizeof($where);
					for($i=0; $i<$arraylength; $i++){
						$sql .= trim($where[$i]);
						if($i < $arraylength-1){
							$sql .= " AND ";
						}
					}
				} else {
					$sql .= " WHERE ".$where;
				}
				if(!$disabled){
					$sql .= " AND DisabledDate = '0000-00-00 00:00:00'";
				}
			} else {
				if(!$disabled){
					$sql .= " WHERE DisabledDate = '0000-00-00 00:00:00'";
				}
			}			
			
			if($order){
				$sql .= " ORDER BY ".$order;
			}
			$sql .= " LIMIT 1";
			
			$this->sqlString = $sql;

			$prepared = $this->databaseHandler->prepare($sql);
			if ($prepared->execute()) {
				while ($row = $prepared->fetch()) {
					if($what){
						$i = 0;
						$rowlist = explode(',',$what);
						foreach($rowlist as $singlerow){
							$tmp[$i] = $row[$i];
							$i++;
						}
					} else {
						for($i=0;$i<$columns;$i++){
							$tmp[$i] = $row[$i];
						}
					}
					$data = $tmp;
				}
			} else {
				throw new Exception('Could not fetch single row from table: '.TPREP.$table);
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			return false;
		}
		return $data;*/
	}	
	
	/**
     * This function finds the value of the last created private key.
	 *
	 * It finds the last entry in the table, it finds the name of the private key
	 * and orders the data after the private key.
	 * so it only needs a table name and if necessary, also a where clause.
	 *
	 * @param string $table the table to use from the database.
	 * @param string $where the conditions.
	 *
	 * @return bool/array Returns array of data on success or FALSE on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */	
	public function readLastEntry($table,$where = ''){
		$data = array();
		try{
			$sql = "SELECT * FROM ".TPREP.$table." ";
			$columns = self::countAffectedColumns($table);
			if(!$columns){
				self::DBug('Fatal error!!');
			}
			if($where){
				if(is_array($where)){
					$sql .= " WHERE ";
					$arraylength = sizeof($where);

					while ($rowData = current($where)) {
						$sql .= key($where)." = ".$rowData." ";
						if($i < $arraylength-1){
							$sql .= "AND ";
						}
						$i++;
						next($where);
					}
				} else {
					$sql .= "WHERE ".$where;
				}
			}
			$sql .= " ORDER BY ".self::getPrivateKey($table)." DESC LIMIT 1";

			$this->sqlString = $sql;
			
			$prepared = $this->databaseHandler->prepare($sql);
			if ($prepared->execute()) {
				while ($row = $prepared->fetch()) {
					for($i=0;$i<$columns;$i++){
						$data[$i] = $row[$i];
					}
				}
			} else {
				throw new Exception('Could not fetch the last entry in table: '.TPREP.$table);
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			return false;
		}
		return $data;
	}
	
	/**
     * This method counts rows in a given table based on the the where clause.
	 *
	 * @param string $table the table to use from the database.
	 * @param string $where the conditions.
	 *
	 * @return int/bool Rows on success or FALSE on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */	
	public function count($table,$where =''){
		$data = 0;
		try{
			$sql = "SELECT count(".self::getPrivateKey($table).") AS counter FROM ".TPREP.$table;
			if($where){
				$sql .= " WHERE ".$where;
			}

			$this->sqlString = $sql;
			
			$prepared = $this->databaseHandler->prepare($sql);
			if ($prepared->execute()) {
				while ($row = $prepared->fetch()) {
					$data = $row['counter'];
				}
			} else {
				throw new Exception('Could not count number of columns in table: '.TPREP.$table);
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
		}
		return $data;	
	}

	/**
     * This method truncates a table
	 *
	 * @param string $table the table to use from the database.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function truncateTable($table){
		if(!$table){
			return false;
		}

		try{
			self::TransactionBegin();
			$sql = 'TRUNCATE TABLE '.$table;
			$this->sqlString = $sql;
			$prepared = $this->databaseHandler->prepare($sql);
			if (!$prepared->execute()) {
				throw new Exception('Could not update data in table: '.TPREP.$table);
			}			
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			self::TransactionRollback();
			return false;
		}	
		self::TransactionEnd();
		return true;
	}
	
	/**
     * This method updates data correlating to a row in the table.
	 * If one of the parsed parameters is empty, the method returns false.
	 * The method accepts an array with keys and values or a string
	 * in normal sql format.
	 *
	 * @param string $table the table to use from the database.
	 * @param string $what what data to update
	 * @param string $where the conditions.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function update($table,$values,$where){
		if(!$table || !$values || !$where){
			return false;
		}

		try{
			self::TransactionBegin();
			$sql = "UPDATE ".TPREP.$table." SET ";

			if(is_array($values)){
				$arraylength = sizeof($values);
				$keyarray = array_keys($values);
				$valarray = array_values($values);
				for($i = 0;$i <= $arraylength; $i++){
					if(isset($keyarray[$i])){
						$sql .= $keyarray[$i]." = ".$valarray[$i];
					}
					if($i < $arraylength-1){
						$sql .= ", ";
					}
				}	
			} else {
				$sql .= $values;
			}
			if(substr(trim($sql), -1) == '='){			
				$sql = substr(trim($sql), 0, -1);
			}
			$sql .= " WHERE ".$where;			
			$this->sqlString = $sql;		
			$prepared = $this->databaseHandler->prepare($sql);
			if (!$prepared->execute()) {
				throw new Exception('Could not update data in table: '.TPREP.$table);
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			self::TransactionRollback();
			return false;
		}	
		self::TransactionEnd();
		return true;
	} 
	
	/**
     * This method deletes a row in the table.
	 *
	 * @param string $table the table to use from the database.
	 * @param string $where the conditions.
	 *
	 * @return bool TRUE on success or FALSE on failure.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */
	public function destroy($table,$where){
		if(!$table || !$where){
			return false;
		}
		try{
			self::TransactionBegin();
			$sql = "DELETE FROM ".TPREP.$table;
			
			if(is_array($where)){
				$sql .= " WHERE ";
				$arraylength = sizeof($where);

				while ($rowData = current($where)) {
					$sql .= key($where)." = ".$rowData." ";
					if($i < $arraylength-1){
						$sql .= "AND ";
					}
					$i++;
					next($where);
				}				
			} else {
				$sql .= " WHERE ".$where;
			}
			$this->sqlString = $sql;
			
			$prepared = $this->databaseHandler->prepare($sql);
			if (!$prepared->execute()) {
				throw new Exception('Could not delete row in table: '.TPREP.$table);
			}			
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
			self::TransactionRollback();
			return false;
		}	
		self::TransactionEnd();
		return true;
	}
	
	/**
     * This method disables a row in the table.
	 * It does this simply, by changing the DisabledDate to the current time.
	 * The method uses the getPrivateKey to find the private key for the table, 
	 * so its not necessary to provide it with the tables private key
	 *
	 * @param string $table the table are we working on
	 * @param int $id The id for the row in the table.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function disable($table,$id){
		$data = array("DisabledDate" => "NOW()");
		$privatekey = self::getPrivateKey($table);
		return self::update($table,$data,$privatekey." = ".$id);	
	}

	/**
     * This method enables a row in the table.
	 * It simply sets the DisabledDate to 0
	 * The methods uses the same approach as the disable method
	 *
	 * @param string $table the table are we working on
	 * @param int $id The id for the row in the table.
	 *
	 * @return bool true on success or false on failure.	 
	 *
	 * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function enable($table,$id){
		$data = array("DisabledDate" => "'0000-00-00 00:00:00'");
		$privatekey = self::getPrivateKey($table);
		return self::update($table,$data,$privatekey." = ".$id);
	}		

	/**
     * This function counts the numbers of columns in a table.
	 * 
	 * This function can count the number of rows in a table.
	 * The information is fetched from MySQL information_schema
	 *
	 * @param string $table the table where the number of columns are counted.
	 *
	 * @return int Returns the number of table rows on success or 0 on failure.
	 *
     * @access private
	 * @since Method available since Release 1.0.0
     */		
	private function countAffectedColumns($table){
		$data = 0;
		try{
			$sql = "SELECT count(*) AS counter FROM information_schema.`COLUMNS` C";
			$sql .= " WHERE table_name = '".TPREP.$table."'";
			$sql .= " AND TABLE_SCHEMA = '".DATABASE."'";
		
			$prepared = $this->databaseHandler->prepare($sql);
			if ($prepared->execute()) {
				while ($row = $prepared->fetch()) {
					$data = $row['counter'];
				}
			} else {
				throw new Exception('Could not count number of columns in table: '.TPREP.$table);
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
	 * @param string $table the table to in the database.
	 *
	 * @return string Returns the name of the row on success or '' on failure.
	 *
     * @access private
	 * @since Method available since Release 1.0.0
     */
	private function getPrivateKey($table){
		$data = '';
		try{
			$sql = "SELECT COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE K";
			$sql .= " WHERE TABLE_NAME = '".TPREP.$table."'";
			$sql .= " AND TABLE_SCHEMA = '".DATABASE."'";
		
			$prepared = $this->databaseHandler->prepare($sql);
			if ($prepared->execute()) {
				while ($row = $prepared->fetch()) {
					$data = $row['COLUMN_NAME'];
				}
			} else {
				throw new Exception('Could not find private key in table: '.TPREP.$table);
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,$sql);
		}		
		return $data;
	}
}
?>