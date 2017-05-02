<?php

class municipalitycontrol{


	public static function indexAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		$searchVal = '';
		if (isset($args['searchfield'])){
			$searchVal = $args['searchfield'];
		}
		$body = views::displayEditListview(municipality::listMunicipalities($searchVal));

		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();	
	}

	public static function findAction($args){
		if (!$args[0]){
			return false;
		}
		list($Municipality) = municipality::readSingleMunicipality($args[0]);
		return $Municipality;
	}
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		if (isset($args[0]) && $args[0]){
			$id = $args[0];		
			list($municipality,$code) = municipality::readSingleMunicipality($id);
		} else {
			$id = 0;
			$municipality = '';
			$code = '';
		}
		
		$body = form::beginForm('update','modules/geography/municipalitycontrol/update');
			$body .= form::fieldset('field1','<h3>'.language::readType('MUNICIPALITY').'</h3>',form::input($municipality,'municipality',TEXT));	
			$body .= form::fieldset('field2','<h3>'.language::readType('MUNICIPALITY_CODE').'</h3>',form::input($code,'code',TEXT));	
			$body .= form::input($id,'id',HIDDEN);
		$body .= form::endForm('update');
		
		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();	
	}
	
	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}

		if (form::validate('update')){
			if ($args['id']){
				municipality::updateMunicipality($args['id'],$args['municipality'],$args['code']);
			} else {			
				municipality::createMunicipality($args['municipality'],$args['code']);
			}
		}
		route::redirect('geography/municipalitycontrol/list');
	}
	
	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Municipality" => "varchar(100)",
					"MunicipalityCode" => "int(10)");

		$result = $databaseadmin->createTable('geography_municipality',$what,"PK_MunicipalityID");
		$databaseadmin->importSQLfile('modules/geography/sql/');		
	}	

	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		municipality::destroyMunicipality($args[0]);
		route::redirect('geography/municipalitycontrol/list');
	}	
}
?>