<?php

class countrycontrol{

	public static function findAction($args){
		// search for country name by its id
		list($countryname) = country::readSingleCountry($id);
		return $countryname;
	}

	public static function indexAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}

		$searchVal = '';
		if (isset($args['searchfield'])){
			$searchVal = $args['searchfield'];
		}		
		$body = views::displayEditListview(country::listCountries($searchVal));

		template::initiate('admin');
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}
	
	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}		
		$databaseadmin = new databaseadmin();
		$what = array("CountryName" => "varchar(100)",
					"CountryCode" => "varchar(100)");
		$result = $databaseadmin->createTable('geography_country',$what,"PK_CountryID");
		
		require_once('system/utils/import.class.php');
		import::importCSV('modules/geography/csv/geography_country.csv','geography_country');
	}	
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		
		if (isset($args[0]) && $args[0]){
			$id = $args[0];		
			list($countryname,$countrycode) = country::readSingleCountry($id);
		} else {
			$id = 0;
			$countryname = '';
			$countrycode = 0;
		}
		
		$body = form::beginForm('update','modules/geography/countrycontrol/update');
			$body .= form::fieldset('field1','<h3>'.language::readType('COUNTRY').'</h3>',form::input($countryname,'countryname',TEXT));	
			$body .= form::fieldset('field2','<h3>'.language::readType('COUNTRY_CODE').'</h3>',form::input($countrycode,'countrycode',TEXT));	
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
				country::updateCountry($args['id'],$args['countryname'],$args['countrycode']);
			} else {
				country::createCountry($args['countryname'],$args['countrycode']);
			}
		}
		route::redirect('geography/countrycontrol/list');
	}	
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		country::destroyCountry($args[0]);
		route::redirect('geography/countrycontrol/list');
	}
}
?>