<?php
namespace Mouf\MVC\BCE\Classes\ValidationHandlers;

use Mouf\MVC\BCE\Classes\ScriptManagers\ScriptManager;

use Mouf\MVC\BCE\BCEForm;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;
use Mouf\MVC\BCE\Classes\Descriptors\Many2ManyFieldDescriptor;
use Mouf\Utils\Common\Validators\JsValidatorInterface;
/**
 * Builds the validation script of a form depending on it's field descriptors and their validators using the jQuery Validate syntax and library
 * 
 * @Component
 * @author Kevin
 *
 */
class JQueryValidateHandler implements JsValidationHandlerInterface {
	
	/**
	 * Contains all the validation functions
	 * @var array<string>
	 */
	private $methods = array();
	
	/**
	 * Contains all the rule to be applied, field by field
	 * @var stdClass
	 */
	private $fieldRules = array();
	
	/**
	 * @Property
	 * @var WebLibrary
	 */
	public $jsLib;

    private $errorClass = 'help-inline';
    private $errorElement = 'span';

    /**
     * Setter for errorClass property
     * errorClass is the css class applied to error messages
     * Default to 'help-inline'
     *
     * @param string $errorClass
     */
    public function setErrorClass($errorClass){
        $this->errorClass = $errorClass;
    }

    /**
     * Setter for errorElement property
     * errorElement is the html tag for the error messages
     * Default to 'span'
     *
     * @param string $errorElement
     */
    public function setErrorElement($errorElement){
        $this->errorElement = $errorElement;
    }
	
	
	public function addJs(BCEForm $form){
		$formId = $form->attributes['id'];
		if(empty($formId)) {
			throw new \Exception('Error while generating JS validation rules, the id of the form ($formId) must be set.');
		}
                
		if (empty($this->fieldRules)){
			return array();
		}
                
		$rulesJson = json_encode($this->fieldRules);
		
		$rulesJson = "
			{
				ignore: [],
				errorPlacement: function(error, element) {
                    var form= element.closest('form');
                    var elementsWithSameName = form.find('[name=\"'+element.attr('name')+'\"]');
                    if(elementsWithSameName.size() == 1){
                        error.insertAfter( element );
                    }else{
                        var findCommonAncestor = function(a, b){
                            if (jQuery.contains(a[0], b[0])){
                                return a;
                            } else if (jQuery.contains(b[0], a[0])){
                                return b;
                            }
                            return a.parents().has(b).first();
                        }
                        var commonAncestor = elementsWithSameName[0];
                        for(var i=1;i<elementsWithSameName.length;i++){
                            commonAncestor = findCommonAncestor(jQuery(commonAncestor), jQuery(elementsWithSameName[i]));
                        }
                        error.appendTo( commonAncestor );
                    }
				},
				errorClass: '".$this->errorClass."',
				errorElement: '".$this->errorElement."'
			}
		";
		
		$js = '
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
		
		foreach ($this->methods as $method) {
			$js.="
				$method			
			";
		} 
		
		$js.= "
			var validateHandler = $('#$formId').validate(
				$rulesJson
			);";

		foreach ($this->fieldRules as $fieldId => $rule) {
			$js.="
			$('.$fieldId').each(function(){
				$(this).rules('add', {".$fieldId.": ".$rule."});
			});
			";
		}
		
		
		$form->scriptManager->addScript(ScriptManager::SCOPE_READY, $js);
	}
	
	public function getJsLibrary(){
		return $this->jsLib;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\ValidationHandlers\JsValidationHandlerInterface::addValidationData()
	 */
	public function addValidationData(JSValidationData $data){
		$this->methods[$data->ruleName] = "
			$.validator.addMethod(
				'$data->ruleName',
				function(value, element) { 
					var functionCall = $data->ruleScript
					return functionCall(value, element);
				},
				'$data->ruleMessage'
			);
		";
		
		$this->fieldRules[$data->ruleName] = $data->ruleArguments;
		
		return array($data->ruleName);
	}
	
}