<?php
class newscontrol {
	public static function indexAction($args){
		if(!$news = news::readUserNews($args[0])){
			route::redirect('modules/cms/news/news');
		}

		template::initiate('main');
			template::header(element::readElementPath(route::$url,'[HEADER]',''));
			template::body(template::useBlock('news_block',$news));
			template::title(element::readElementPath(route::$url,'[TITLE]',TITLE));
			template::footer(element::readElementPath(route::$url,'[FOOTER]',USERFOOTER));			
			template::replace('[COPY]',element::readElementPath(route::$url,'[COPY]',COPYFOOTER));
			template::replace('[MENU]',menu::makeMenu());	
		template::end();	
	}
	
	public static function newsAction($args){		
		template::initiate('main');
			template::header('');
			template::body(template::useBlock('news_block',news::listUserNews(0,5)));
			template::title(element::readElementPath(route::$url,'[TITLE]',TITLE));
			template::footer(element::readElementPath(route::$url,'[FOOTER]',USERFOOTER));			
			template::replace('[COPY]',element::readElementPath(route::$url,'[COPY]',COPYFOOTER));
			template::replace('[MENU]',menu::makeMenu());	
		template::end();		
	}

	public static function listAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}		
		$body = '<div id="edit"></div>';		
		$body .= views::displayEditListview(news::listNews(1));
		$body .= form::newButton();		
		
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();	
	}
	
	public static function deleteAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		news::destroyNews($args[0]);
		route::redirect('modules/cms/usernews/list');
	}
	
	public static function editAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		
		list($id,$headline,$teaser,$bodytext,$online,$offline,$icon,$sticky) = news::readNews($args[0]);

		$fieldset = array("style" => "width:920px;border:0px;");	
		$date = array("class" => "form-control", "style" => "width:220px", "onClick" => "displayDate(this.id,'',1)");	
		$teaserstyle = array("style" => "width:920px;height:220px;");
		$textstyle = array("style" => "width:920px;height:420px;");

		$body = form::beginForm('update','modules/cms/news/update');
			$body .= form::fieldset('field1','<h3>'.language::readType('STICKY').'</h3>',form::check($sticky,'sticky'),$fieldset);
			$body .= form::fieldset('field2','<h3>'.language::readType('ONLINE').'</h3>',form::input($online,'online',0,$date),$fieldset);
			$body .= form::fieldset('field3','<h3>'.language::readType('OFFLINE').'</h3>',form::input($offline,'offline',0,$date),$fieldset);
			$body .= form::fieldset('field4','<h3>'.language::readType('ICON').'</h3>',form::select(files::listFolderContent('uploads/medium'),'java.png','icon',1),$fieldset);
			
			$body .= form::fieldset('field5','<h3>'.language::readType('HEADLINE').'</h3>',form::input($headline,'headline',0,array("style" => "width:620px")),$fieldset);	
			$body .= form::fieldset('field6','<h3>'.language::readType('TEASER').'</h3>',form::textarea($teaser,'teaser',$teaserstyle),$fieldset).'<br />';
			$body .= form::fieldset('field7','<h3>'.language::readType('TEXT').'</h3>',form::textarea($bodytext,'bodytext',$textstyle),$fieldset).'<br />';
			$body .= form::input($id,'id',2);
			$body .= form::input($args['update'],'update',2);
		$body .= form::endForm('update');	
				
		template::initiate('admin');
			template::noCache();
			template::header(language::readType('EDIT'));
			template::body($body);	
		template::end();
	}
	
	public static function updateAction($args){
		if(!user::validateAdmin()){
			route::error(403);
		}
		if($args['sticky'] == 'on'){
			$sticky = 1;
		} else {
			$sticky = 0;
		}

		if(form::validate('update')){
			if($args['id']){
				news::updateNews($args['id'],$args['headline'],$args['teaser'],$args['bodytext'],$args['online'],$args['offline'],$args['icon'],$sticky);
			} else {			
				news::createNews($args['headline'],$args['teaser'],$args['bodytext'],$args['online'],$args['offline'],$args['icon'],$sticky);
			}
		}
		route::redirect('modules/cms/news/list');
	}	
	
	public static function installAction(){
		if(!user::validateAdmin()){
			if(user::countUser('ADMIN')){
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
		$lan = STD_LANGUAGE;
		if($args['lan']){
			$lan = $args['lan'];
		}
		template::initiate('rss');
			template::noCache();
			template::replace('[DATE]',date("F j, Y, g:i a"));
			template::body(template::useBlock('rss_block',news::listRssNews()));
		template::end();
	}		
}
?>