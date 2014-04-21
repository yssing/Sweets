<?php
class databasecontrol{

	public static function indexAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}		
	}

	public static function listAction(){
		if(!user::validateAdmin()){
			route::error(403);
		}
		
		$database = new database();		
		$body = views::displayEditListview($database->showTables(DATABASE));
		$body .= '<h2>This is only a quick and simple database editor, in no way is it meant to replace managers like phpmyadmin!</h2>';
		$body .= form::beginField('head1',language::readType('ADDTABLE'));
			$body .= form::beginForm('add',PATH_WEB.'/system/database/addtable');	
				$body .= form::fieldset('field1',language::readType('TABLENAME'),form::input('','table_name',TEXT));
				$body .= form::fieldset('field2',language::readType('PRIVATEKEY'),form::input('','privatekey',TEXT));
				$body .= form::fieldset('field4',language::readType('CONFIRM'),form::input('','confirm',TEXT).' (Y)');
			$body .= form::endForm('add');
		$body .= form::endField();
		$body .= form::beginField('head2',language::readType('DROPTABLE'));
			$body .= form::beginForm('drop',PATH_WEB.'/system/database/drop');	
				$body .= form::fieldset('field3',language::readType('TABLENAME'),form::input('','table_name',TEXT));
				$body .= form::fieldset('field4',language::readType('CONFIRM'),form::input('','confirm',TEXT).' (Y)');
			$body .= form::endForm('drop');
		$body .= form::endField();
		
		template::initiate('admin');
			template::header('Database');
			template::body($body);	
		template::end();		
	}	
	
	public static function editAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}	
		$columns = array();
		$database = new database();
		$table = $database->showColumns($args[0]);
		$body = views::displayListview($table);
		//build 2D array
		foreach($table as $row){
			$tmp = str_replace(TPREP,'',$row[0]);
			$columns[] = array($tmp,$tmp);
		}
		
		$body .= form::beginField('head1',language::readType('ADDCOLUMN'));
			$body .= form::beginForm('add',PATH_WEB.'/system/database/addcolumn');	
				$body .= form::fieldset('field1',language::readType('NAME'),form::input('','name',TEXT));		
				$body .= form::fieldset('field2',language::readType('TYPE'),form::input('','type',TEXT));
				$body .= form::fieldset('field4',language::readType('CONFIRM'),form::input('','confirm',TEXT).' (Y)');
				$body .= form::input($args[0],'table_name',HIDDEN);
			$body .= form::endForm('add');
		$body .= form::endField();
		$body .= form::beginField('head2',language::readType('CHANGETOUTF8'));
			$body .= form::beginForm('change',PATH_WEB.'/system/database/changeutf8');	
				$body .= form::fieldset('field3',language::readType('NAME'),form::select($columns,'','column_name'));
				$body .= form::fieldset('field4',language::readType('CONFIRM'),form::input('','confirm',TEXT).' (Y)');
				$body .= form::input(str_replace(TPREP,'',$args[0]),'table_name',HIDDEN);
			$body .= form::endForm('change');
		$body .= form::endField();		
		$body .= form::beginField('head3',language::readType('TRUNCATETABLE'));
			$body .= form::beginForm('truncate',PATH_WEB.'/system/database/truncate');	
				$body .= form::fieldset('field5',language::readType('CONFIRM'),form::input('','confirm',TEXT).' (Y)');
				$body .= form::input($args[0],'table_name',HIDDEN);
			$body .= form::endForm('truncate');
		$body .= form::endField();	
		
		template::initiate('admin');
			template::header('Database');
			template::body($body);	
		template::end();
	}	
	
	/**
	 * This action will change a row in a given table to utf8 text format.
	 * It is located here, because it needs to be some where, this is as good a place
	 * as any other.
	 * The first argument is the table to use and the second argument is which column.
	 */	
	public static function changeutf8Action($args){
		if(!user::validateAdmin()){
			route::error(403);
		}	
		$database = new database();
		if(form::validate('change') && $args['confirm'] == 'Y'){
			$database->changeToUTF8($args['table_name'],$args['column_name']);	
		}
		route::redirect('system/database/edit/'.$args['table_name']);
	}
	
	public static function addtableAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		if(form::validate('add') && $args['confirm'] == 'Y'){
			$database = new database();
			$database->createTable($args['table_name'],' ',$args['privatekey']);
		}
		route::redirect('system/database/edit/'.TPREP.$args['table_name']);
	}
	
	public static function truncateAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		$database = new database();
		if(form::validate('truncate') && $args['confirm'] == 'Y'){
			$database->truncateTable($args['table_name']);
		}
		route::redirect('system/database/edit/'.$args['table_name']);
	}
	
	public static function dropAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		$database = new database();
		if(form::validate('drop') && $args['confirm'] == 'Y'){
			echo $database->dropTable(trim($args['table_name']));
		}
		route::redirect('system/database/list');		
	}
	
	public static function addcolumnAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		$database = new database();
		if(form::validate('add') && $args['confirm'] == 'Y'){
			$database->addColumn($args['table_name'],$args['name'],$args['type']);
		}	
		route::redirect('system/database/edit/'.$args['table_name']);
	}
	
	public static function importAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		if(form::validate('import')){
			//$database->importTable($args['table_name'],$args['type'],$args['file']);
		}
	}
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		route::redirect('system/database/edit/'.$args[0]);
	}
}
?>