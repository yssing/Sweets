<?php
	/**
	 * The index example
	 * This example uses the frontpage template.
	 * Its important to not use replace([PATH]) until everything has been
	 * injected correctly!
	 * Let the designers worry about the template and the css that goes with it!
	 * The developer can worry about doing the development!
	 */
class frontpage {
	
	public static function indexAction(){
		template::initiate('frontpage');
			template::header(element::readElementPath(route::$url,'[HEADER]',USERHEADER));
			template::body(element::readElementPath(route::$url,'[BODY]','Dummy text'));

			template::replace('[SCROLLER1]',element::readElementPath(route::$url,'[SCROLLER1]','Php Development'));
			template::replace('[SCROLLER2]',element::readElementPath(route::$url,'[SCROLLER2]','Java Development'));
			template::replace('[SCROLLER3]',element::readElementPath(route::$url,'[SCROLLER3]','C/C++ Development'));
			template::replace('[SCROLLER4]',element::readElementPath(route::$url,'[SCROLLER4]','Basic Development'));
			template::replace('[SCROLLER5]',element::readElementPath(route::$url,'[SCROLLER5]','Arduino Development'));
			template::replace('[NEWS]',template::useBlock('news_block',news::listUserNews(0,5)));
			
			template::title(element::readElementPath(route::$url,'[TITLE]',TITLE));
			template::footer(element::readElementPath(route::$url,'[FOOTER]',USERFOOTER));

			template::replace('[COPY]',element::readElementPath(route::$url,'[COPY]',COPYFOOTER));
			template::replace('[PATH]',PATH_WEB);
			template::replace('[MENU]',menu::makeMenu());
		template::end();
	}
}
?>