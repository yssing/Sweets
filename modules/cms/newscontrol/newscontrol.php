<?php
class newscontrol {
	public static function indexAction($args){
		if (!$news = news::readUserNews($args[0])){
			route::redirect('modules/cms/news/news');
		}
		$iconpath = '';
		if(!modules::isModule('ephoto')){
			$iconpath = 'uploads/images/full/';
		}
		template::initiate('main');		
			template::noCache();
			template::setValue('headline',$news['Headline']);
			template::setValue('teaser',$news['Teaser']);
			template::setValue('body',$news['BodyText']);
			template::setValue('icon',$iconpath.$news['icon']);
			template::setValue('user',$news['UserFirstName'].' '.$news['UserLastName']);
			template::setValue('createdate',$news['CreateDate']);
			template::body(template::useBlock('news_block'));
			
			template::header('');
			template::title(text::readTextByKey('TITLE'));
			template::footer(text::readTextByKey('FOOTER'));
			template::replace('[COPY]',text::readTextByKey('COPY'));	
			template::replace('[MENU]',menu::makeMenu());
		template::end();	
	}
	
	public static function newsAction($args){
		template::initiate('main');
			template::header('');
			$news_template = '';
			$iconpath = '';
			if(!modules::isModule('ephoto')){
				$iconpath = 'uploads/images/full/';
			}
			
			foreach(news::listUserNews() as $newsline){
				template::setValue('headline',$newsline['Headline']);
				template::setValue('teaser',$newsline['Teaser']);
				template::setValue('body','<a href="/cms/news/'.$newsline['PK_NewsID'].'">['.language::readType('READMORE').']</a>');
				template::setValue('icon',$iconpath.$newsline['icon']);
				template::setValue('user',$newsline['UserFirstName'].' '.$newsline['UserLastName']);
				template::setValue('createdate',$newsline['CreateDate']);
				$news_template .= template::useBlock('news_block');
			}
			template::body($news_template);
			template::title(text::readTextByKey('TITLE'));
			template::footer(text::readTextByKey('FOOTER'));
			template::replace('[COPY]',text::readTextByKey('COPY'));	
			template::replace('[MENU]',menu::makeMenu());
		template::end();
	}

	public static function listAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		$body = '';
		$body .= views::displayEditListview(news::listNews(1));
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
		template::end();
	}
	
	public static function deleteAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		news::destroyNews($args[0]);
		route::redirect('modules/cms/usernews/list');
	}
	
	public static function editAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}		
		if (isset($args[0])){
			list($id,$headline,$teaser,$bodytext,$online,$offline,$icon,$sticky) = news::readNews($args[0]);
		} else {
			$id = $headline = $teaser = $bodytext = $online = $offline = $icon = $sticky = '';
		}
		if ($offline == '0000-00-00 00:00:00' || !$offline){
			$offline = calendar::add_date(calendar::now(),365);
		}
		if ($online == '0000-00-00 00:00:00' || !$online){
			$online = calendar::now();
		}		
	
		//$date = array("class" => "form-control", "data-date-format" => "yyyy-mm-dd");	
		$teaserstyle = array("style" => "height:220px;");
		$textstyle = array("style" => "height:420px;");
		$ephoto = array("onClick" => "showePhotoSel('icon','single')");

		$body = form::beginForm('update','modules/cms/news/update');
			$body .= form::fieldset('field1',language::readType('STICKY'),form::check($sticky,'sticky'));
			$body .= form::fieldset('field6',language::readType('ONLINE'),form::inputControl($online,'online','<img src="[TEMPLATE]/icon/calendar.png">'));
			$body .= form::fieldset('field7',language::readType('OFFLINE'),form::inputControl($offline,'offline','<img src="[TEMPLATE]/icon/calendar.png">'));			
			
			if(modules::isModule('ephoto')){
				$body .= photo::showControl($icon);
			} else {
				$body .= form::fieldset('field4',language::readType('ICON'),form::select(files::listFolderContent('uploads/images/medium'),$icon,'icon',1));
			}
			
			$body .= form::fieldset('field5',language::readType('HEADLINE'),form::input($headline,'headline',0));	
			$body .= form::fieldset('field6',language::readType('TEASER'),form::textarea($teaser,'teaser',$teaserstyle));
			$body .= form::fieldset('field7',language::readType('TEXT'),form::textarea($bodytext,'bodytext',$textstyle));
			$body .= form::input($id,'id',2);
			if (isset($args['update'])){
				$body .= form::input($args['update'],'update',2);
			} else {
				$body .= form::input(0,'update',2);
			}
		$body .= form::endForm('update');	
		
		$javascript = '
			$(function(){
				$("#online").datetimepicker({format:"Y-m-d H:i:s", step:30});
				$("#offline").datetimepicker({format:"Y-m-d H:i:s", step:30});
			});
		';		
	
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);
			template::injectJavascript($javascript);
		template::end();
	}
	
	public static function updateAction($args){
		if (!user::validateAdmin()){
			route::error(403);
		}
		if (isset($args['sticky'])){
			$sticky = 1;
		} else {
			$sticky = 0;
		}
		$id = '';
		//if (form::validate('update')){
			if ($args['id']){
				$id = $args['id'];
				news::updateNews($args['id'],$args['headline'],$args['teaser'],$args['bodytext'],$args['online'],$args['offline'],$args['icon'],$sticky);
			} else {
				$id = news::createNews($args['headline'],$args['teaser'],$args['bodytext'],$args['online'],$args['offline'],$args['icon'],$sticky);
			}
		//}
		//route::redirect('modules/cms/news/list');
		route::redirect('modules/cms/news/edit/'.$id);
	}	
	
	public static function installAction(){
		if (!user::validateAdmin()){
			if (user::countUser('ADMIN')){
				route::error(403);
			}
		}
		$databaseadmin = new databaseadmin();
		$what = array("Headline" => "varchar(200)",
					"Teaser" => "text",
					"BodyText" => "text",
					"Sticky" => "tinyint(1)",
					"OnlineDate" => "datetime",
					"OfflineDate" => "datetime",
					"Icon" => "varchar(200)");
		$result = $databaseadmin->createTable('cms_news',$what,"PK_NewsID");
	}	
	
	public static function rssAction($args){
		template::initiate('rss');
			template::noCache();
			$news_template = '';			
			$iconpath = '';
			if(!modules::isModule('ephoto')){
				$iconpath = 'uploads/images/full/';
			}			
			
			foreach(news::listUserNews() as $newsline){
				template::setValue('path_web',PATH_WEB);
				template::setValue('headline',$newsline['Headline']);
				template::setValue('teaser',$newsline['Teaser']);
				template::setValue('id',$newsline['PK_NewsID']);
				template::setValue('icon',$iconpath.$newsline['icon']);
				template::setValue('user',$newsline['UserFirstName'].' '.$newsline['UserLastName']);
				template::setValue('createdate',$newsline['CreateDate']);
				$news_template .= template::useBlock('rss_block');
			}
			template::replace('[DATE]',date("F j, Y, g:i a"));
			template::body($news_template);
		template::end();
	}		
}
?>