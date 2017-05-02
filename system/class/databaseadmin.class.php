<?php
/**
 * This class handles database administration 
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
 * @require		'database.class.php'
 */
include_once('dbobject.class.php');
class databaseadmin extends dbobject{

	public function __construct(){
		parent::__construct();
		//if (!user::validateAdmin()){
		//	route::error('403');
		//}		
	}
	
	/**
     * This function shows the databases that the database object has access to
	 * ONLY USE THIS AS ADMINISTRATOR!!
	 *
	 * @return array $data returns an array of table names.
	 *
     * @access public
	 * @since Method available since Release 1.0.0
     */		
	public function showDatabases(){
		$data = array();
		if (baseclass::$adminid){
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
		if (baseclass::$adminid){
			$sql = 'SHOW TABLES ';
			if ($database){
				$sql .= 'FROM '.$database;
			}

			$outerLoop = $this->databaseHandler->prepare($sql);
			if ($outerLoop->execute()) {
				while ($row = $outerLoop->fetch()) {
					if ($database){
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
		$this->dbobject_table = $table;
		$data = array();
		if (baseclass::$adminid){
			$sql = 'SHOW COLUMNS ';
			if ($table){
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
		if (!$table || !$name || !$type){
			return false;
		}
		$this->dbobject_table = $table;
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
	public function createTable($table,$what = '',$privatekey = '',$charset = 'utf8'){
		if (!$table || !$what){
			return false;
		}
		$this->dbobject_query = $table;
		try{
			self::TransactionBegin();
			if (!$privatekey){
				$privatekey = 'PK_'.$table.'ID';
			}

			$sql = 'CREATE TABLE IF NOT EXISTS `'.TPREP.$table.'` (
			`'.$privatekey.'` int(10) unsigned NOT NULL AUTO_INCREMENT,';
			
			if ($what != ''){
				if (is_array($what)){
					while ($rowData = current($what)) {
						if (stripos($rowData,'NOT NULL')){
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
				throw new Exception('Could not create new table: '.$table);
				return false;
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
	 * This method can add an index to a cloumn in a  given table
	 *
	 * If the user does not have the correct permission, then it will throw an error!
	 *
	 * @param string $table the table to be created.
	 * @param string $column what column is the index added to.
	 *
	 * @return boolean true on success or false on failure
	 *
     * @access public
	 * @since Method available since Release 26-01-2017
	 */			
	public function addIndex($table, $column){
		if (!$table || !$column){
			return false;
		}

		$this->dbobject_query = $table;
		try{
			self::TransactionBegin();

			$sql = 'ALTER TABLE `'.TPREP.$table.'` ADD INDEX(`'.$column.'`);';

			$this->sqlString = $sql;

			$innerLoop = $this->databaseHandler->prepare($sql);
			if (!$innerLoop->execute()) {
				throw new Exception('Could not create index: '.$column.' in: '.$table);
				return false;
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
	 * This method imports a SQL file, if any, into a table, set by the createTable function
	 * It will truncate the table on import!
	 *
	 * @param string $sqlPath the path where the sql is found.
	 * @param string $truncate should the table be truncated first, defaults to true.
	 *
	 * @return boolean true on success or false on failure
	 *
     * @access public
	 * @since Method available since Release 1.0.0
	 */		
	public function importSQLfile($sqlPath, $truncate = true){
		if (!is_file($sqlPath.$this->dbobject_query.'.sql')){
			return false;
		}
		if($truncate){
			self::truncateTable(TPREP.$this->dbobject_query);
		}
		
		// loads the sql file
		$sql = file_get_contents($sqlPath.$this->dbobject_query.'.sql');
		// insert TPREP to the table name
		$sql = str_replace('`'.$this->dbobject_query.'`','`'.TPREP.$this->dbobject_query.'`',$sql);
		
		//self::TransactionBegin();
		try{
			$prepared = $this->databaseHandler->prepare($sql);

			if (!$prepared->execute()) {
				throw new Exception('Could not import Query');
			}
		}	
		catch (Exception $e){
			self::DBug('Caught exception: '. $e->getMessage(),__METHOD__,'Import SQL File:'.$sqlPath.$this->dbobject_query.'.sql');
			//self::TransactionRollback();
			return false;
		}
		//self::TransactionEnd();		
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
		if (!$table){
			return false;
		}	
		try{
			self::TransactionBegin();
			$sql = 'DROP TABLE IF EXISTS `'.$table.'`';
			$innerLoop = $this->databaseHandler->prepare($sql);
			if (!$innerLoop->execute()) {
				throw new Exception('Could not drop table: '.$table);
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
		$this->db_table = $table;
		$key = self::getPrivateKey();
		$sql = "SELECT ".$key.", ".$what." FROM ".TPREP.$table;

		try{
			$outerLoop = $this->databaseHandler->prepare($sql);
			if ($outerLoop->execute()) {
				while ($row = $outerLoop->fetch()) {
				
					$newname = utf8_encode($row[$what]);				
					$innerSQL = "UPDATE ".$this->db_table." SET ".$what." = '".$newname."' WHERE ".$key." = ".$row[$key];
					try{
						$newname = utf8_encode($row[$what]);				
						$innerSQL = "UPDATE ".$this->db_table." SET ".$what." = '".$newname."' WHERE ".$key." = ".$row[$key];					
						$innerLoop = $this->databaseHandler->prepare($innerSQL);
						if (!$innerLoop->execute()) {
							throw new Exception('Could not update column '.$what.' in '.$this->db_table.' to utf 8!');
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
		if (!$table){
			return false;
		}

		try{
			self::TransactionBegin();
			$sql = 'TRUNCATE TABLE '.$table;
			$this->sqlString = $sql;
			$prepared = $this->databaseHandler->prepare($sql);
			if (!$prepared->execute()) {
				throw new Exception('Could not truncate table: '.$table);
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

}
?>