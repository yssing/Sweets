       <?php
     /**
       * Package Name: smartSEO
       * Package URI: http://www.codia.tk/smartseo
       * Description: Very comprehensive seo analysis.Count of backlinks,pagerank etc.
       * Version: 1.2
       * Author: Trev Tune
       * Demo URI: http://demos.codia.tk/smartseo
       * License: GPL2
       */
       
     /*Copyright 2013  Trev Tune  (email : jayzantel@gmail.com).
       This program is free software; you can redistribute it and/or modifyit under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.This program is distributed in the hope that it will be useful,but WITHOUT ANY WARRANTY; without even the implied warranty ofMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.You should have received a copy of the GNU General Public Licensealong with this program; if not, write to the Free SoftwareFoundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
       */
       
       
       class smartSEO{
       protected $page;
       public $rankInfo;
       public $rankDetails;
       protected $ok=false;
       protected $url;
       
       function __construct($url=false)
       {
       if (!$url)
       { return; }
       $this->ok=true;
       
   //Get the page contents
       $this->page=$this->get($url);
       $this->url=$url;
       
   //Remove html,js,css etc
       $this->page=$this->clean($this->page);
       
       }
       
     /*Opens a file or url
       *and fetches its contents
       *There were issues with curl on some hosts
       *thats why we used file_get_contents()
       */
       
       protected function get($url=false)
       {
       if (!$url)
       {
       $this->error='You have not defined a url to process';
       return false;
       }
       $data= @file_get_contents($url);
       if (!$data)
       {
       $this->error='We could not fetch' . $url .'.It might be unavailable';
       return false;
       }
       return $data;
       
       }
       
     /*Removes html,js etc
       *Gets rid of multiple spaces
       *@param $html ->the content to be stripped
       */
       
       function clean($html=false)
       {
       if (!$html)
       {
       $this->error='You need to specify the html before you can clean it';
       return false;
       }
       $html=trim($html);
       
   //Remove scripts
       $html=preg_replace('#<script[^>]*>.*?</script>#is','',$html);
       
   //Now remove css
       $html=preg_replace('#<style[^>]*>.*?</style>#is','',$html);
       $html =strip_tags($html);
       
   // Unify terminators
       $html=preg_replace('/[\.!?]/','.',$html);
       
   // Replace new lines with spaces
       $html=preg_replace('/[ ]*(\n|\r\n|\r)[ ]*/',' ',$html);
       
   // Check for duplicated terminators
       $html=preg_replace('/([\.])[\. ] /','$1',$html);
       
   // Remove multiple spaces
       $html=preg_replace('/[ ] /',' ',$html);
       
       return $html;
       }
       
     /*Converts a string to a number
       *e.g if you input 1jg62,236,58 it returns 16223658
       */
       
       function strToNumber($no)
       {
       $no=preg_replace('#[^0-9]#','',$no);
       return (int) $no;
       }
       
     /*Returns details (array) about a given string
       *@if param1 is not supplied the default is used
       */
       
       function textDetails($text=false)
       {
       
       if (!$text)
       {
       $text=$this->page;
       }else{
       $text=$this->clean($text);
       }
       
   //Calculate number of sentences
   //Warning : evaluates false positives for mr. Etc
       $this->textDetails['sentences'] =  strlen(preg_replace('/[^\.!?]/', '', $text));
       
       $report='<h2 style="color:green;">Page analysis by smartSEO</h2>';
       
   //Get word count
       $this->textDetails['words'] =str_word_count($text);
       
       if ($this->textDetails['words'] <180)
       {
       $msg=' <code style="color:red;">which is way below the recommended number.</code><br/>';
       }
       
       elseif ($this->textDetails['words'] <300)
       {
       $msg=' <code style=style="color:#FF2525;">which is almost the recommended lenght.Add more usefull content.</code><br/>';
       }
       elseif ($this->textDetails['words'] <400)
       {
       $msg='.<code style="color:brown;">Your content has the required number of words.</code><br/>';
       }
       else
       {
       $msg='.<code style="color:green;">Congratulations.Your document is huge enough to use more keywords without spamming.</code><br/>';
       }
       
       $report .=" For a good seo score,you need to write a minimum 300 words.This document has " . $this->textDetails['sentences'] . " sentences and " . $this->textDetails['words'] . " words " . $msg;
       
       
       $this->textDetails['averageWordsPerSentence'] =round($this->textDetails['words']/$this->textDetails['sentences'],2);
       
       
       $wordlist = explode(' ', $text);
   //$wordlist=str_word_count($text,1);
       $syllables=0;
       
       for ($i = 0; $i < count($wordlist); $i++)
       {
       $syllables = $syllables + $this->countSyllables($wordlist[$i]);
       }
       $this->textDetails['averageSyllablesPerWord']
       =round($syllables /  count ($wordlist),4);
       
       
       $report .="<br/>It has an average of " .  $this->textDetails['averageWordsPerSentence'] . "  words per sentence and " .  $this->textDetails['averageSyllablesPerWord'] . " syllables per word ."
       ;
       
       
     /*Calculate the flesch reading score of the document (rounded)
       *The higher the score the easier it is to read the document
       *Score ranges from 5-120
       *Have a look at wikipedia.org for more details
       *http://en.m.wikipedia.org/wiki/Flesch%E2%80%93Kincaid_readability_tests
       */
       
       
       $this->textDetails['fleschScore']=round(206.835 -  (1.015 * $this->textDetails['averageWordsPerSentence']) - (84.6 * $this->textDetails['averageSyllablesPerWord']));
       
     /*Calculates the flesch grade
       *the lower the result the better
       */
       
       $this->textDetails['fleschGrade']=round((.39 * $this->textDetails['averageWordsPerSentence']) + (11.8 * $this->textDetails['averageSyllablesPerWord']) - 15.59);
       
       if ($this->textDetails['fleschScore'] <30)
       {
       $msg=' very hard ';
       }
       
       elseif ($this->textDetails['fleschScore'] <45)
       {
       $msg=' fairly hard ';
       }
       
       elseif ($this->textDetails['fleschScore'] <60)
       {
       $msg=' fairly easy ';
       }
       
       elseif ($this->textDetails['fleschScore'] <80)
       {
       $msg=' easy ';
       }
       
       elseif ($this->textDetails['fleschScore'] >79)
       {
       $msg=' very easy ';
       }
       
       
       $report .='<br/>Your document is ' . $msg . ' to read. It has scored a flesch reading score of ' . $this->textDetails['fleschScore'] . ' and a flesch grade of' . $this->textDetails['fleschGrade'] . '.Check out <a href="http://en.m.wikipedia.org/wiki/Flesch%E2%80%93Kincaid_readability_tests">flesch on wikipedia</a> to find out how it is calculated.';
       
       return $report;
       
       }
       
   //End of function textDetails
       
       
     /*Counts the number of syllables in a word
       */
       function countSyllables($word)
       {
       
       $subsyl = Array( 'cial' ,'tia' ,'cius' ,'cious' ,'giu' ,'ion' ,'iou' ,'sia$' ,'.ely$' );
       
       $addsyl = Array( 'ia' ,'riet' ,'dien' ,'iu' ,'io' ,'ii' ,'[aeiouym]bl$' ,'[aeiou]{3}' ,'^mc' ,'ism$' ,'([^aeiouy])\1l$' ,'[^l]lien' ,'^coa[dglx].' ,'[^gq]ua[^auieo]' ,'dnt$' );
       
       $word = preg_replace('/[^a-z]/is', '', strtolower($word));
       
       $valid_word_parts=array();
       $word_parts = preg_split('/[^aeiouy]+/', $word);
       
       foreach ($word_parts as $key => $value)
       {
       if ($value <> '')
       {
       $valid_word_parts[] = $value;
       }
       }
       $syllables = 0;
       
       foreach ($subsyl as $syl)
       {
       $syllables -= preg_match('~'.$syl.'~', $word);
       }
       foreach ($addsyl as $syl)
       {
       $syllables += preg_match('~'.$syl.'~', $word);
       }
       if (strlen($word) == 1)
       {
       $syllables++;
       }
       $syllables += count($valid_word_parts);
       $syllables = ($syllables == 0) ? 1 : $syllables;
       return $syllables;
       }/*Generates the report
       *By default it prints the report
       *If you need to get the report as a string pass true as the parameter.
       */
       
       function getreport($return=false)
       {
       
       if (!$this->ok)
       {
       echo $this->error;
       return false;
       }
       
       $startTime=microtime();
       
       $pageanalysis=$this->textDetails();
       
       $rankinfo=$this->getRankInfo($this->url);
       
       $endTime=microtime();
       
       $report="<div>Report calculated in " . $endTime-$startTime . " seconds.<br/>";
       
       $report .= $rankinfo . '<br>' . $pageanalysis . '</div>';
       
       if (!$return)
       {
       echo $report;
       return true;
       }
       return $report;
       }/*get several rank information
       *traffic ,alexarank,pagerank etc
       */
       
       function getRankInfo($domain=false)
       {
       if (!$domain)
       {
       $domain=$this->url;
       }
       
   //get the domain from a url
       
       $domain=$this->extractDomainFromUrl($domain);
       
   //Fetch the data from wow
       $data=$this->get('http://www.worthofweb.com/website-value/' . $domain);
       
   //Remove html,js etc
       $data=$this->clean($data);
       
   //The rank will be an array and a report
       
       $this->rankInfo=array();
       $report='<div style="color:green;"> Statistics for ' . $domain . '</div>';
       
   //Remove unnecessary details
       
       $data=strtolower(str_replace(array(':','?','$','/',' ','\n','\t','\r','\r\n','\n\r'),'',$data));
       
   //Get the domain worth
       preg_match("#(?:$domain)isworth(.*?)-#i", $data,$worth);
       $this->rankInfo['worth']=$worth[1];
       
       $report .=$domain . 'is worth $ ' . $worth[1] . '<br/>';
       
   //Get google pagerank
       preg_match('#\bGooglePageRank([0-9F])#i',$data,$pg);
       
       
       $this->rankInfo['pagerank']=$pg[1];
       $report .='Google Pagerank : ' . $pg[1] . '<br/>NOTE:Google uses pagerank to determine a websites worth.A pagerank of 10 has higher chances of appearing in SERP than a pagerank of 0.Check out  <a href="http://searchengineland.com/what-is-google-pagerank-a-guide-for-searchers-webmasters-11068">this guide about pagerank</a><br/>';
       
   //Get traffic details
       $array=array(
       "pageviews"=>array("day","month","year"),
       "visitors"=>array("day","month","year"));
       
       foreach($array as $key=>$value)
       {
       for($i=0;$i<count($value);$i++)
       {
       $word=$key . $array[$key][$i];
       
       preg_match("#([0-9\,?]*)$word#i",$data,$traffic);
       
       $this->rankInfo[$key][$array[$key][$i]]=$traffic[1];
       
       $report .='<br/><span style="color:#446a5' .$i. ' ">Total ' . $key . ' per ' . $array[$key][$i] . ' : ' . $traffic[1] . '</span>';
       
       }
       }
       
   //Get alexa rank for the last 3 months
       
       preg_match("#alexarank([0-9\,?]*)#i", $data,$alexa);
       $this->rankInfo['alexa']=$alexa[1];
       $report .=" $domain has an alexa global rank of $alexa[1] . This rank is updated daily.";
       
   //Into something we all love
       
       
       preg_match ("#howmuchdoesitmake(.*?)day(.*?)month(.*?)year#imsu",$data,$earns);
       
       $this->rankInfo['earnings']['day']=$earns[1];
       
       $this->rankInfo['earnings']['month']=$earns[2];
       
       $this->rankInfo['earnings']['year']=$earns[3];
       
       $report .='<br/>With good advertising, the site should be able to earn $' .  $earns[1] . ' per day, $' . $earns[2] . ' per month  and  $' . $earns[3] . 'per year.Note that this site could earn well more or less than the stated amount.It all depends on how the owner advertises.for a start,here is a great article by Daniel Socco on ways to <a href="http://www.dailyblogtips.com/ways-to-make-money-online-with-website/"> make money with your website</a>';
       
       return $report;
       }
       
       function extractDomainFromUrl($url)
       {
       $domain= preg_replace(
       array(
       '~^https?\://~si' ,// strip protocol
       '~[/:#?;%&].*~',// strip port, path, query, anchor, etc
       '~\.$~',// trailing period
       ),
       '',$url);
       
       if (preg_match('#^www.(.*)#i',$domain))
       {
       $domain=preg_replace('#www.#i','',$domain);
       }
       return $domain;
       }
       
       }