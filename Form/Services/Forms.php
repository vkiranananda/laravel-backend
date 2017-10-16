<?php 

namespace Backend\Form\Services;

use Illuminate\Support\ViewErrorBag;
use Illuminate\Support\Facades\Request;
use Illuminate\Routing\Controller;
use Backend\Core\Services\Helpers;


class Forms
{
	static public function attributes($attributes, $ignore = array())
	{
		$res  = '';

		foreach ($attributes as $key => $value) {
			if($value != '' && array_search($key, $ignore) === false ){
				$res .= ' '.$key.'="'.$value.'"';
			}
		}
		return $res;
	}

	static private function valueCheck($options = array())
	{
		if(!isset($options['value'])) $options['value'] = '';

		if(isset($options['html-value'])){
			unset($options['html-value']);
			return $options;
		}

		$options['value'] = htmlentities($options['value']);

		return $options;
	}

	static private function inputField($options = array())
	{
		if(!isset($options['value']))$options['value'] = '';
		return "<input type='".$options['type']."' value='".$options['value']."' ".Forms::attributes(Forms::valueCheck($options['attr'])).">";
	}

	static public function textareaField($options = array())
	{		
		$options = Forms::valueCheck($options);

		return "<textarea ".Forms::attributes($options['attr']).">".$options['value']."</textarea>";
	}

	static public function mceField($options = array())
	{		
		$options = Forms::valueCheck($options);

		return "<textarea data-role='tinyMCE' ".Forms::attributes($options['attr']).">".$options['value']."</textarea>";
	}

	static public function hiddenField($options = array())
	{
		return Forms::inputField($options);
	}

	static public function passwordField($options = array())
	{
		return Forms::inputField($options);
	}

	static public function textField($options = array())
	{
		return Forms::inputField($options);
	}

	static public function dateField($options = array())
	{
		return Forms::inputField($options);
	}

	static public function emailField($options = array())
	{
		return Forms::inputField($options);
	}

	static public function telField($options = array())
	{
		return Forms::inputField($options);
	}

	static public function selectField($options = array())
	{		
		if(!isset($options['options']) || !is_array($options['options']))return '';

		$out = "<select ".Forms::attributes($options['attr']).">";
		if(!isset($options['value']))$options['value'] = '';
		foreach ($options['options'] as $el) {
			// dd($el);
			$out .= "<option ".Forms::attributes($el, ['label']);
			
			if($el['value'] == $options['value']) $out .= " selected";
			$out .= ">".$el['label']."</option>";
		}
		$out .= "</select>\n";
		return $out;
	}

	static public function checkboxField($options = [])
	{		
		if(!isset($options['options']) || !is_array($options['options']))return '';

		$out = '<div class="row">';
		$options['name'] .= "[]";
		foreach ($options['options'] as $el) {
			$out .= '<div class="form-check ';
			$out .= (isset($options['class-con'])) ? $options['class-con'] : 'col-12' ;
			$out .= '"><label class="form-check-label">';
			$out .= "<input value='".$el['value']."' type=\"checkbox\" ".Forms::attributes($options['attr']);
			
			if(isset($options['value'] )){
				if( Helpers::optionsSearch($options['value'],  $el['value'] ) ) $out .= " checked";
			}
			$out .= ">&nbsp;".$el['label'];
			$out .= "</label></div>\n";
		}
		return $out."</div>";
	}


	static public function radioField($options = array())
	{		
		if(!isset($options['options']) || !is_array($options['options']))return false;
		
		$res = '';
		$id = 2;

		foreach ($options['options'] as $key => $el) {
		
			$out = "<input type='radio' ";

			if($el['value'] == $options['value']) $out .= "checked";

			$out .= " ".Forms::attributes($options['attr']).' '.Forms::attributes($el, ['label'])."> ".$el['label'];

			$res .= "<label class=\"form-check-label\">$out</label>\n";
			$options['attr']['id'] = $options['attr']['id']."".$id++;
		}
		return $res;
	}

	static public function fieldHTML($options = [], &$data = [])
	{
		if(!isset($options['type']))return false;

		$fn = $options['type'].'Field';
		
		echo Forms::$fn($options);
	}
}