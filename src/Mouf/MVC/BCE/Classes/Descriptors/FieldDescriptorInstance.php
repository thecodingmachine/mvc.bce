<?php
namespace Mouf\MVC\BCE\Classes\Descriptors;

use Mouf\MVC\BCE\Classes\ValidationHandlers\JsValidationHandlerInterface;

use Mouf\MVC\BCE\BCEForm;

use Mouf\MVC\BCE\Classes\ValidationHandlers\JSValidationData;
use Mouf\MoufManager;
use Mouf\Utils\Common\Validators\JsValidatorInterface;
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;

class FieldDescriptorInstance implements FieldDescriptorInstanceInterface {
	
	/**
	 * @var FieldDescriptor
	 */
	public $fieldDescriptor;
	
	/**
	 * @var BCEForm
	 */
	public $form;
	
	/**
	 * @var mixed
	 */
	public $value;
	
	/**
	 * Validator list
	 * @var string[]
	 */
	protected $validators = array();
	
	/**
	 * @var int
	 */
	private $beanId;
	
	private $containerName;

	public function __construct(BCEFieldDescriptorInterface $descriptor, BCEForm $form, $beanId){
		$this->fieldDescriptor = $descriptor;
		$this->form = $form;
		$this->beanId = $beanId;
	}
	
	public function setContainerName($name){
		$this->containerName = $name;
	}
	
	public function getFieldName(){
		$fieldName = $this->fieldDescriptor->getFieldName();
		return $this->form->isMain ? $fieldName : $this->containerName . "[$this->beanId][$fieldName]";  
	}
	
	public function getFieldValue(){
		return $this->value;
	}

	public function getBeanId(){
		return $this->beanId;
	}
	
	public function setFieldValue($value){
		$this->value = $value;
	}

	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface::addValidationData()
	 */
	public function addValidationData(JsValidationHandlerInterface &$handler) {
		$i = 0;
		if (!isset($this->fieldDescriptor->validators)){
			return;
		}
		foreach ($this->fieldDescriptor->validators as $validator){
			/* @var $validator JsValidatorInterface */
			if ($validator instanceof JsValidatorInterface){
				$validationRule = MoufManager::getMoufManager()->findInstanceName($validator);
				$validationData = new JSValidationData($this->getFieldName(), $validator->getScript(), $validationRule, $validator->getErrorMessage(), $validator->getJsArguments());
				$this->validators = array_merge($handler->addValidationData($validationData), $this->validators);
				$i++;
			}
		}
	}
	
	/**
	 * Return array of all classes for validator.
	 * Add it on your html element.
	 * @return string[]
	 */
	public function getValidator() {
		return $this->validators;
	}
/*	
	private function mergeAttributes($attributes){
		foreach ($attributes as $attrName => $attrValues){
			switch ($attrName) {
				case "id":
				case "name":
					continue;
				break;
				case "class":
				case "style":
					if (!is_array($attrValues)){
						throw new Exception("Attribute values for '$attrName' should be an array");
					}
					foreach ($attrValues as $value){
						if (!isset($this->attributes[$attrName])){
							$this->attributes[$attrName] = array();
						}
						array_push($this->attributes[$attrName], $value);
					}
				break;
				default:
					$this->attributes[$attrName] = $attrValues;
				break;
			}
		}
	}
	
	public function printAttributes(){
		$strAttr = "";
		foreach ($this->attributes as $attrName => $attrValues){
			switch ($attrName) {
				case "id":
				case "name":
					continue;
				break;
				case "class":
				case "style":
					$strAttr .= " $attrName='".implode(" ", $attrValues)."'";
				break;
				default:
					$strAttr .= " $attrName='$attrValues'";
				break;
			}
		}
		return $strAttr;
	}
	*/
	public function toHtml($formMode){
		if (!$this->fieldDescriptor->canView() && $formMode == 'view'){
			return "";
		}
		
		echo $this->fieldDescriptor->toHTML($this, $formMode);
	
// 		$fieldHtml = $this->fieldDescriptor->toHTML($this, $formMode);
//		$fieldHtml = "";
		//echo $fieldHtml !== false ? $this->fieldDescriptor->getFieldWrapperRenderer()->render($this, $fieldHtml, $formMode) : "";
	}
	
}