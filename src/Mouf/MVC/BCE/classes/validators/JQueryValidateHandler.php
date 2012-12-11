<?php
namespace Mouf\MVC\BCE\classes\validators;

use Mouf\MVC\BCE\classes\Many2ManyFieldDescriptor;
use Mouf\Utils\Common\Validators\JsValidatorInterface;
use Mouf\MVC\BCE\classes\FieldDescriptor;

/**
 * Builds the validation script of a form depending on it's field descriptors and their validators using the jQuery Validate syntax and library
 * 
 * @Component
 * @author Kevin
 *
 */
class JQueryValidateHandler implements JsValidationHandlerInterface{
	
	/**
	 * Contains all the validation functions
	 * @var array<string>
	 */
	private $validationMethods;
	
	/**
	 * Contains all the rule to be applied, field by field
	 * @var stdClass
	 */
	private $validationRules;
	
	/**
	 * @Property
	 * @var WebLibrary $jsLib
	 */
	public $jsLib;
	
	private function wrapRule(FieldDescriptor $fieldDescriptor, JsValidatorInterface $validator, $ruleIndex){
		return "
			$.validator.addMethod(
				'".$fieldDescriptor->getFieldName()."_rule_$ruleIndex',
				function(value, element) { 
					var functionCall = ".$validator->getScript()."
					return functionCall(value, element);
				},
				'".str_replace("{fieldName}", $fieldDescriptor->getFieldLabel(), $validator->getErrorMessage())."'
			);";
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see JsValidationHandlerInterface::buildValidationScript()
	 */
	public function buildValidationScript(FieldDescriptor $descriptor, $formId){
		if (!count($descriptor->getValidators())){
			return;
		}
		
		$i = 0;
		$fieldName = $descriptor->getFieldName();
		$validators = $descriptor->getValidators();
		
		$realFieldName = $descriptor instanceof Many2ManyFieldDescriptor ? $fieldName."[]" : $fieldName;
		
		foreach ($validators as $validator) {
			if ($validator instanceof JsValidatorInterface) {
				$this->validationMethods[] = $this->wrapRule($descriptor, $validator, $i);
				$methodName = $fieldName."_rule_".$i;
				$this->validationRules->rules->$realFieldName->$methodName = $validator->getJsArguments();
				$i++;
			}
		}
	}
	
	public function getValidationJs($formId){
		if(empty($formId)) {
			throw new \Exception('Error while generating JS validation rules, the id of the form ($formId) must be set.');
		}
                
                if (empty($this->validationRules)){
                    return "";
                }
                
		$rulesJson = json_encode($this->validationRules->rules);
		
		$rulesJson = "
			{
				rules: $rulesJson,
				errorPlacement : function(error, element){
					var id = element.attr('id');
					var htmlElem = document.getElementById(id);
					var type = htmlElem.nodeName.toLowerCase();
					if (type == 'input' && _checkable(htmlElem)){
						error.insertAfter($('input[name='+htmlElem.name+']:checkbox:last').parent());
					}else{
						error.insertAfter(element);
					}
					element.closest('.control-group').addClass('error');
				},
				errorClass: 'help-inline',
				errorElement: 'span'
			}
		";
		
		$js = '
		$(document).ready(function(){
			_currentForm =document.getElementById("'.$formId.'");
			
			_checkable =  function( element ) {
				return /radio|checkbox/i.test(element.type);
			};
			
			_findByName = function( name ) {
				// select by name and filter by form for performance over form.find("[name=...]")
				var form = _currentForm;
				return $(document.getElementsByName(name)).map(function(index, element) {
					return element.form == form && element.name == name && element  || null;
				});
			};
			
			_getLength = function(value, element) {
				switch( element.nodeName.toLowerCase() ) {
				case "select":
					return $("option:selected", element).length;
				case "input":
					if( _checkable( element) )
						return _findByName(element.name).filter(":checked").length;
				}
				return value.length;
			};
		
			_depend = function(param, element) {
				return _dependTypes[typeof param]
					? _dependTypes[typeof param](param, element)
					: true;
			};
		
			_dependTypes = {
				"boolean": function(param, element) {
					return param;
				},
				"string": function(param, element) {
					return !!$(param, element.form).length;
				},
				"function": function(param, element) {
					return param(element);
				}
			};
		';
		
                if (isset($this->validationMethods)){
                   foreach ($this->validationMethods as $method) {
			$js.="
				$method			
			";
                    } 
                }
		
		$js.= "
			var validateHandler = $('#$formId').validate(
				$rulesJson
			);
		});";
		
		
		return $js;
	}
	
	public function getJsLibrary(){
		return $this->jsLib;
	}
	
}