<?php
class databasecontrol{

	public static function indexAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}		
	}

	public static function listAction(){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		$databaseadmin = new databaseadmin();		
		$body = views::displayEditListview($databaseadmin->showTables(DATABASE));
		$body .= '<h4>This is only a simple database editor, in no way is it meant to replace managers like phpmyadmin!</h4>';
		$body .= form::beginField('head1',language::readType('ADD_TABLE'));
			$body .= form::beginForm('add',PATH_WEB.'/system/database/addtable');	
				$body .= form::fieldset('field1',language::readType('TABLE_NAME'),form::input('','table_name',TEXT));
				$body .= form::fieldset('field2',language::readType('PRIVATE_KEY'),form::input('','privatekey',TEXT));
				$body .= form::fieldset('field4',language::readType('CONFIRM'),form::inputControl('','confirm','(Y)'));
			$body .= form::endForm('add');
		$body .= form::endField();
		$body .= form::beginField('head2',language::readType('DROP_TABLE'));
			$body .= form::beginForm('drop',PATH_WEB.'/system/database/drop');	
				$body .= form::fieldset('field3',language::readType('TABLE_NAME'),form::input('','table_name',TEXT));
				$body .= form::fieldset('field4',language::readType('CONFIRM'),form::inputControl('','confirm','(Y)'));
			$body .= form::endForm('drop');
		$body .= form::endField();
		
		template::initiate('admin');
			template::header('Database');
			template::body($body);	
		template::end();		
	}	
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}	
		$columns = array();
		$databaseadmin = new databaseadmin();
		$table = $databaseadmin->showColumns($args[0]);
		$body = views::displayListview($table);
		//build 2D array
		foreach($table as $row){
			$tmp = str_replace(TPREP,'',$row[0]);
			$columns[] = array($tmp,$tmp);
		}
		// add column
		$body .= form::beginField('head1',language::readType('ADD_COLUMN'));
			$body .= form::beginForm('add',PATH_WEB.'/system/database/addcolumn');	
				$body .= form::fieldset('field1',language::readType('NAME'),form::input('','name',TEXT));		
				$body .= form::fieldset('field2',language::readType('TYPE'),form::input('','type',TEXT));
				$body .= form::fieldset('field4',language::readType('CONFIRM'),form::inputControl('','confirm','(Y)'));
				$body .= form::input($args[0],'table_name',HIDDEN);
			$body .= form::endForm('add');
		$body .= form::endField();
		// change to utf8
		$body .= form::beginField('head2',language::readType('CHANGE_TO_UTF8'));
			$body .= form::beginForm('change',PATH_WEB.'/system/database/changeutf8');	
				$body .= form::fieldset('field3',language::readType('NAME'),form::select($columns,'','column_name'));
				$body .= form::fieldset('field4',language::readType('CONFIRM'),form::inputControl('','confirm','(Y)'));
				$body .= form::input(str_replace(TPREP,'',$args[0]),'table_name',HIDDEN);
			$body .= form::endForm('change');
		$body .= form::endField();
		// Add index
		$body .= form::beginField('head3',language::readType('ADD_INDEX'));
			$body .= form::beginForm('change',PATH_WEB.'/system/database/addindex');	
				$body .= form::fieldset('field5',language::readType('NAME'),form::select($columns,'','column_name'));
				$body .= form::fieldset('field6',language::readType('CONFIRM'),form::inputControl('','confirm','(Y)'));
				$body .= form::input(str_replace(TPREP,'',$args[0]),'table_name',HIDDEN);
			$body .= form::endForm('change');
		$body .= form::endField();			
		// Truncate
		$body .= form::beginField('head4',language::readType('TRUNCATE_TABLE'));
			$body .= form::beginForm('truncate',PATH_WEB.'/system/database/truncate');	
				$body .= form::fieldset('field7',language::readType('CONFIRM'),form::inputControl('','confirm','(Y)'));
				$body .= form::input($args[0],'table_name',HIDDEN);
			$body .= form::endForm('truncate');
		$body .= form::endField();	
		
		template::initiate('admin');
			template::header('Database');
			template::body($body);	
		template::end();
	}	
	
	/** 
	 * This action adds an index to a given column.
	 */	
	public static function addindexAction($args){	
		if (!user::validateAdmin()){
			route::error(403);
		}	
		if (form::validate('change') && $args['confirm'] == 'Y'){
			$databaseadmin = new databaseadmin();
			$databaseadmin->addIndex($args['table_name'],$args['column_name']);
		}
		route::redirect('system/database/edit/'.TPREP.$args['table_name']);
	}
	
	/**
	 * This action will change a row in a given table to utf8 text format.
	 * It is located here, because it needs to be some where, this is as good a place
	 * as any other.
	 * The first argument is the table to use and the second argument is which column.
	 */	
	public static function changeutf8Action($args){
		if (!user::validateAdmin()){
			route::error(403);
		}	
		if (form::validate('change') && $args['confirm'] == 'Y'){
			$databaseadmin = new databaseadmin();
			$databaseadmin->changeToUTF8($args['table_name'],$args['column_name']);	
		}
		route::redirect('system/database/edit/'.TPREP.$args['table_name']);
	}
	
	public static function addtableAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		if (form::validate('add') && $args['confirm'] == 'Y'){
			$databaseadmin = new databaseadmin();
			$databaseadmin->createTable($args['table_name'],' ',$args['privatekey']);
		}
		route::redirect('system/database/edit/'.TPREP.$args['table_name']);
	}
	
	public static function truncateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$databaseadmin = new databaseadmin();
		if (form::validate('truncate') && $args['confirm'] == 'Y'){
			$databaseadmin->truncateTable($args['table_name']);
		}
		route::redirect('system/database/edit/'.TPREP.$args['table_name']);
	}
	
	public static function dropAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$databaseadmin = new databaseadmin();
		if (form::validate('drop') && $args['confirm'] == 'Y'){
			echo $databaseadmin->dropTable(trim($args['table_name']));
		}
		route::redirect('system/database/list');		
	}
	
	public static function addcolumnAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$databaseadmin = new databaseadmin();
		if (form::validate('add') && $args['confirm'] == 'Y'){
			$databaseadmin->addColumn($args['table_name'],$args['name'],$args['type']);
		}	
		route::redirect('system/database/edit/'.TPREP.$args['table_name']);
	}
	
	public static function importAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$databaseadmin = new databaseadmin();
		if (form::validate('import')){
			//$databaseadmin->importTable($args['table_name'],$args['type'],$args['file']);
		}
	}
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		route::redirect('system/database/edit/'.$args[0]);
	}
}
?>