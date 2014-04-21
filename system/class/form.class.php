<?php
/**
 * This class handles all different form operations.
 *
 * Copyright (C) <2014> <Frederik Yssing>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category	Generic system methods
 * @package		forms
 * @author		Frederik Yssing <yssing@yssing.org>
 * @copyright	2012-2014 Yssing
 * @version		SVN: 1.0.0
 * @link		http://www.yssing.org
 * @since		File available since Release 1.0.0
 * @todo		Finish the methods for form generation.
 */
require_once('generic.IO.class.php');
class form extends genericIO{

	/**
	 * the name of the form is stored in the variable.
	 *
	 * @acess public
	 * @static
	 */
	public static $formName = '';

	/**
	 * This method validates a form input.
	 * 
	 * This method checks if the returned form hash is the same as
	 * the sessions hash. 
	 * Strictly speaking, this is not necessary, but by using this its possible
	 * to avoid forms re-updating data by hitting f5 and importantly to avoid form hacking.
	 * If a page have more than one form to check and render, then all that is needed is
	 * specify what form validate should check.
	 *
	 * @return bool return true on validation or false if not.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function validate($name = ''){
		if($name){
			self::$formName = $name;
		}
		if($_REQUEST['formsalt'.self::$formName] == $_SESSION['formsalt'.self::$formName] && $_SESSION['formsalt'.self::$formName]){
			return true;
		}else{
			return false;
		}
	}	

	/**
	 * This method starts the rendering of a form.
	 *
	 * This methods simply generate the form header based on various parameters.
	 * it is possible to add basically anything to the header by parsin an associative array.
	 *
	 * @param string $name The name of the form.
	 * @param string $action What script to post to.
	 * @param array $settings An array with everything else.
	 *
	 * @return string $input This is the header of a form.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function beginForm($name,$action='',$settings=''){
		if($action){
			$action = 'action="'.$action.'"';
		} else {
			$action = 'action="'.$_SERVER['PHP_SELF'].'"';
		}
		self::$formName = $name;
		$input = '<form name="'.$name.'" id="'.$name.'" method="post" '.$action.' ';
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$input .=  key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}
		$input .= ' enctype="multipart/form-data">';
		return $input;
	}
	
	/**
	 * This method ends the rendering of a form.
	 *
	 * When ending the form, this method makes sure to add
	 * a submit button a given value and it also creates
	 * a form hash for security. It takes the value of the formName
	 * and add that to the names of the submit and the hash field, this
	 * makes it possbile to have several forms on one page.
	 * It will as standard implement a submit button, but this can
	 * be avoided by setting $submit to false.
	 * It can be usefull to implement another submit button, when the form
	 * needs to activate a javascript rather than the receiver page.
	 *
	 * @param string $value The value of the submit button.
	 * @param bool $submit Use the standard submit or implement your own.
	 *
	 * @return string $input This is the header of a form.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */			
	public static function endForm($value,$submit = true){
		$formsalt = self::generateRandStr(32);
		$_SESSION['formsalt'.self::$formName] = $formsalt;
		$ret = self::input($formsalt,'formsalt'.self::$formName,2);
		if($submit){
			$ret .= self::submit($value,self::$formName,1);
		}
		return $ret.'</form>';
	}		

	/**
	 * Starts creating a fieldset
	 *
	 * @param string $name the name and id of the input box.
	 * @param string $legend The text in the legend.
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $input Returns the fieldset formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function beginField($name,$legend='',$settings=''){
		$field = '<fieldset name="'.$name.'" id="'.$name.'" ';
	
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$field .= key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}	
		$field .= '>';
		if($legend){
			$field .= '<legend>'.$legend.'</legend>';
		}
		return $field;
	}
	
	/**
	 * Ends the fieldset
	 *
	 * @return string returns the fieldset closure.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function endField(){
		return '</fieldset>';
	}

	/**
	 * Starts creating a fieldset
	 *
	 * It can inject a form element in the fields html as well
	 * as regular formatted text.
	 * This can be usefull where only a single element needs to be injected.
	 *
	 * @param string $name the name and id of the input box.
	 * @param string $legend The text in the legend.
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $input Returns the fieldset formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function fieldset($name,$legend='',$data='',$settings=''){
		$field = '<fieldset name="'.$name.'" id="'.$name.'" ';
	
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$field .= key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}	
		$field .= '>';
		if($legend){
			$field .= '<legend>'.$legend.'</legend>';
		}
		if($data){
			$field .= $data;
		}
		$field .= '</fieldset>';
		return $field;
	}

	/**
	 * Creates an image that can be used as a button
	 *
	 * @param string $name the name and id of the image.
	 * @param string $source the path to the image. 
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $image Returns the image formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function image($name,$source,$settings=''){
		$image = '<image name="'.$name.'" id="'.$name.'" ';
		$image .= 'src="'.$source.'" ';
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$image .= key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}	
		$image .= ' border="0" />';
		return $image;	
	}
	
	/**
	 * Creates a label
	 *
	 * It can inject a form element in the labels html as well
	 * as regular formatted text.
	 *
	 * @param string $name the name and id of the label.
	 * @param string $data the html to inject. 
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $label Returns the label formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function label($name,$data,$settings=''){
		$label = '<label name="'.$name.'" id="'.$name.'" ';
	
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$label .= key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}	
		$label .= '>';
		if($data){
			$label .= $data;
		}
		$label .= '</label>';
		return $label;	
	}
	
	/**
	 * Creates an input box
	 *
	 * @param string $data the value of the input box.
	 * @param string $name the name and id of the input box.
	 * @param int $type what type is the input box: text, password or hidden.
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $input Returns the input box formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function input($data,$name,$type,$settings=''){
		if(is_int($type)){
			switch($type){
				case 0: $type = 'text'; break;		
				case 1: $type = 'password'; break;
				case 2: $type = 'hidden'; break;	
				default: $type = 'text'; break;
			}
		}
		
		$input = '<input type="'.$type.'" name="'.$name.'" id="'.$name.'" value="'.$data.'" ';
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$input .=  key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}
		$input .= ' />';
		return $input;
	}
	
	/**
	 * Creates a file upload box
	 *
	 * @param string $data the value of the input box.
	 * @param string $name the name and id of the input box.
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $input Returns the input box formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function file($data,$name,$settings=''){
		$input = '<input type="file" name="'.$name.'" id="'.$name.'" value="'.$data.'" ';
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$input .=  key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}
		$input .= ' />';
		return $input;		
	}	
	
	/**
	 * Creates a textarea and fills it with data, if any data is parsed
	 *
	 * @param string $value the value of the textarea.
	 * @param string $name the name and id of the textarea.
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $textarea Returns the textarea formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function textarea($value,$name,$settings=''){
		$textarea = '<textarea name="'.$name.'" id="'.$name.'" ';
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$textarea .= key($settings).'="'.$rowData.'" ';
				next($settings);
			}
		}
		$textarea .= '>'.$value.'</textarea>';
		return $textarea;
	}	
	
	/**
	 * Creates a button, either submit or regular button
	 *
	 * @param string $value the value of the button.
	 * @param string $name the name and id of the button.
	 * @param bool $type what type is the button, submit or button.
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $input Returns the button formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */			
	public static function submit($value,$name,$type,$settings=''){
		if(is_int($type)){
			if($type){
				$type = 'submit';
			} else {
				$type = 'button';
			}
		}
		
		$input = '<input type="'.$type.'" name="'.$name.'" id="'.$name.'" value="'.$value.'"';
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$input .=  key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}
		$input .= ' />';
		return $input;	
	}
	
	/**
	 * Creates a reset button
	 *
	 * @param string $name the name and id of the checkbox.
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $input Returns the reset button formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function reset($name = 'reset', $settings = ''){
		$input = '<input type="reset" name="'.$name.'" id="'.$name.'"';
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$input .=  key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}
		$input .= ' />';	
	
		return $input;
	}
	
	/**
	 * Creates a checkbox
	 *
	 * @param int $checked is the checkbox checked or not.
	 * @param string $name the name and id of the checkbox.	 
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $input Returns the checkbox formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function check($checked = 0, $name = 'checkbox', $settings = ''){
		$input = '<input type="checkbox" name="'.$name.'" id="'.$name.'"';
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$input .=  key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}
		$input .= self::retCheck($checked);
		$input .= ' />';	
	
		return $input;
	}
	
	/**
	 * Creates a link that opens a new dialog
	 *
	 * @param string $args Does the button need any particular argument.	 
	 * @param string $icon This holds the icon of the link.	 
	 *
	 * @return string $input Returns the link formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function newButton($args = 'edit/', $icon = '/template/icon/add.png'){
		return '<a href='.$args.'>'.language::readType('CREATE').'</a>';		
	} 	

	/**
	 * Displays a 2D array as a range of radio buttons.
	 *
	 * This method takes a 2D array, only the first 2 columns are used, but all of the rows
	 * it can also take different variables to create the radio buttons in various ways.
	 *
	 * @param array $data the data to format and display.
	 * @param string $select the preselected value, can be empty.
	 * @param string $name the name and id of the dropdown box.
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $radio Returns the radio buttons formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */	
	public static function radio($data,$select,$name,$settings = ''){
		$localSettings = '';
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$localSettings .=  key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}	
		
		$radiobutton = '';
		foreach($data as $value){
			if($value[0] == $select){
				$checked = self::returnCheck(1);
			} else {
				$checked = '';
			}	
			$radiobutton .= '<input type="radio" id="'.$name.'" name="'.$name.'" value="'.$value[0].'" '.$checked.' '.$localSettings.' /> '.$value[1];	
		}		

		return $radiobutton;	
	}	
	
	/**
	 * Displays a 2D array in a dropdown box
	 *
	 * This method takes a 2D array, only the first 2 columns are used.
	 * It can also take different variables to create the dropdown in various ways.
	 *
	 * @param array $data the data to format and display.
	 * @param string $select the preselected value, can be empty.
	 * @param string $name the name and id of the dropdown box.
	 * @param int $showzero is the first value of the dropdown a prefilled zero value.
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $dropdown Returns the dropdown formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function select($data,$select,$name,$showzero = 1, $settings = ''){
		$dropdown = '<select name="'.$name.'" id="'.$name.'"';
		
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$dropdown .=  key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}
		$dropdown .= ' >';
		
		if($showzero){
			$dropdown .= '<option value="0">---</option>';
		}
		
		foreach($data as $value){
			if($value[0] == $select){
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$val = isset($value[2]) ? $value[2] : '';
			$dropdown .= '<option '.$val.' value="'.$value[0].'" '.$selected.'>'.$value[1].'</option>';
		}
		$dropdown .= "</select>";
		return $dropdown;
	}
	
	/**
	 * Displays creates a dropdown filled with predefined hours and minutes.
	 *
	 * This method can create dropdowns used in e.g. schemas where it is usefull
	 * to have an option for selecting intervals. It can create the interval down to 15 minutes.	 
	 *
	 * @param string $select the preselected value, can be empty.
	 * @param string $name the name and id of the dropdown box.
	 * @param int $onlyhalves set to 1 to skip the 15 minutes interval.
	 * @param array $settings holds all kinds of formatting and listening	 
	 *
	 * @return string $dropdown Returns the dropdown formatted in html.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */		
	public static function timeInDropDown($select,$name,$onlyhalves = 0, $settings = ''){
		$dropdown = '<select name="'.$name.'" id="'.$name.'"';
		
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$dropdown .=  key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}
		$dropdown .= ' >';

		for($i=0; $i<=23; $i++){
			$tmpi = '';
			$tmpj = '';
			if($i < 10){
				$tmpi = '0'.$i;
			} else {
				$tmpi = $i;
			}
			if($onlyhalves){
				for($j=0; $j<=30; $j+=30){
					if(!$j){
						$tmpj = '00';
					} else {
						$tmpj = $j;
					}
					$time_num = $tmpi.':'.$tmpj;
					if($time_num == $select){
						$selected = 'selected';
					} else {
						$selected = '';
					}
					$dropdown .= '<option value="'.$time_num.'" '.$selected.'>'.$time_num.'</option>';
				}		
			} else {
				for($j=0; $j<=45; $j+=15){
					if(!$j){
						$tmpj = '00';
					} else {
						$tmpj = $j;
					}
					$time_num = $tmpi.':'.$tmpj;
					if($time_num == $select){
						$selected = 'selected';
					} else {
						$selected = '';
					}
					$dropdown .= '<option value="'.$time_num.'" '.$selected.'>'.$time_num.'</option>';
				}
			}
		}
		$dropdown .= "</select>";
		return $dropdown;
	}	
	
	/**
	 * Creates a list of checkboxes based on a 2D array.
	 *
	 * This method creates a list of checkboxes based on values in a 2D array.
	 * The checkboxes can be preselected and styled
	 *
	 * @param array $data the data to format and display.
	 * @param string $selectValue the preselected value, can be empty.
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $selectbox the html with the checkboxes.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */			
	public static function displayCheck($data,$selectValue,$settings = ''){
		$selectbox = '';
		$localSettings = '';
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$localSettings .=  key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}		
		foreach($data as $value){
			$select = '';
			foreach($selectValue as $Singlevalue){
				if($Singlevalue == $value[0]){
					$select = self::retCheck(1);
				}
			}			
			$selectbox .= '<input type="checkbox" id="select'.$value[0].'" name="select'.$value[0].'" value="'.$value[0].'" '.$select.' '.$localSettings.' /> '.$value[1];
		}
		return $selectbox;		
	}	
	
	/**
	 * Translates an int/bool to xhtml version of the checked value.
	 *
	 * @param int $data the value to boolean check on.	 
	 *
	 * @return string $check Return the checked status formatted in html.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */			
	public static function retCheck($bool){
		if($bool){
			return ' checked="checked" ';
		} else {
			return '';
		}
	}	
	
	/**
	 * Displays a 2D array as a list of radio buttons
	 *
	 * This method takes a 2D array, with n amount of rows, use the first column as
	 * the value and the second column as the label
	 *
	 * @param array $data the data to format and display.
	 * @param string $selectValue the preselected value, can be empty.
	 * @param string $name the name of the radio buttons.
	 * @param array $settings holds all kinds of formatting and listening
	 *
	 * @return string $list Returns the dropdown formatted in html.
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */
	public static function displayRadio($data,$selectValue,$name,$settings = ''){
		$radiobutton = '';
		$localSettings = '';
		if(is_array($settings)){
			while ($rowData = current($settings)) {
				$localSettings .=  key($settings).'="'.$rowData.'"';
				next($settings);
			}
		}			
		foreach($data as $value){
			if($value[0] == $selectValue){
				$selected = self::retSelected(1);
			} else {
				$selected = '';
			}	
			$radiobutton .= '<input type="radio" id="'.$name.'" name="'.$name.'" value="'.$value[0].'" '.$selected.' '.$localSettings.' /> '.$value[1];	
		}
		return $radiobutton;
	}	
	
	/**
	 * Translates an int/bool to xhtml version of the selected value.
	 *
	 * @param int $data the value to boolean check on.	 
	 *
	 * @return string $check Return the selected status formatted in html.	 
	 *
	 * @access public
	 * @static
	 * @since Method available since Release 1.0.0
	 */			
	public static function retSelected($bool){
		if($bool){
			return ' selected="selected" ';
		} else {
			return '';
		}
	}
}
?>