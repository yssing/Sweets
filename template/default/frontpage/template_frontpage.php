<?php

/**
 * This is used to control how the frontpage loads.
 * It's also used to load templates for other site specifik blocks.
 * It is only the frontpage that is an actual Action!.
 *
 * The easiest thing is to have this class extend the template class.
 */

class template_frontpage extends template {
	public static function indexAction(){
		self::initiate('frontpage');
			self::header(self::userHeader());
			self::body(text::readTextByKey('BODY'));

			self::replace('[SLIDER]',self::userSlider());			
			self::replace('[NEWS]',self::userNews());
			
			self::title(text::readTextByKey('TITLE'));
			self::menu(submenu::makeMenu('MENU'));
			self::footer(self::userFooter());
			self::replace('[FOOTER]',self::userFooter());

		self::end();			
	}
	
	public static function userNews(){
		$news_template = '';
		$news = news::listUserNews();
		if( is_array($news) ){
			foreach($news as $newsline){
				self::setValue('headline',$newsline['Headline']);
				self::setValue('teaser',$newsline['Teaser']);
				self::setValue('body','<a href="/cms/news/'.$newsline['PK_NewsID'].'">['.language::readType('READMORE').']</a>');
				self::setValue('icon',$newsline['icon']);
				self::setValue('user',$newsline['UserFirstName'].' '.$newsline['UserLastName']);
				self::setValue('createdate',$newsline['CreateDate']);
				$news_template .= self::useBlock('news_block');
			}
		}
		return $news_template; 
	}
	
	public static function userHeader(){
		self::setValue('header',text::readTextByKey('HEADER'));
		return self::useBlock('header');	
	}

	public static function userFooter(){
		self::setValue('footer',text::readTextByKey('FOOTER'));
		self::setValue('copyfooter',text::readTextByKey('COPY'));

		return self::useBlock('footer');
	}

	public static function userSlider(){
		self::setValue('SCROLLER1',text::readTextByKey('SCROLLER1'));
		self::setValue('SCROLLER2',text::readTextByKey('SCROLLER2'));
		self::setValue('SCROLLER3',text::readTextByKey('SCROLLER3'));
		self::setValue('SCROLLER4',text::readTextByKey('SCROLLER4'));
		self::setValue('SCROLLER5',text::readTextByKey('SCROLLER5'));
		self::setValue('SCROLLER6',text::readTextByKey('SCROLLER6'));

		return self::useBlock('slider');
	}

	public static function adminHeader(){
		
	}

	public static function adminFooter(){
		
	}	
}
?>