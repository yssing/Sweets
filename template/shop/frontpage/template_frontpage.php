<?php

class template_frontpage {
	public static function indexAction(){
		template::initiate('frontpage');
			template::header(text::readTextByKey('HEADER'));
			template::body(text::readTextByKey('BODY'));

			$news_template = '';
			$news = news::listUserNews();
			if(is_array($news)){
				foreach($news as $newsline){
					template::setValue('headline',$newsline['Headline']);
					template::setValue('teaser',$newsline['Teaser']);
					template::setValue('body','<a href="/cms/news/'.$newsline['PK_NewsID'].'">['.language::readType('READMORE').']</a>');
					template::setValue('icon',$newsline['icon']);
					template::setValue('user',$newsline['UserFirstName'].' '.$newsline['UserLastName']);
					template::setValue('createdate',$newsline['CreateDate']);
					$news_template .= template::useBlock('news_block');
				}
			}
			template::replace('[NEWS]',$news_template);

			template::title(text::readTextByKey('TITLE'));
			template::footer(text::readTextByKey('FOOTER'));
			template::copy(text::readTextByKey('COPY'));

			template::replace('[PATH]',PATH_WEB);
			template::menu(menu::makeMenu());
		template::end();
	}
}
?>