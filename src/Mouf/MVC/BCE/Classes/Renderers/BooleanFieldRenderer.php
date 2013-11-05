<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\Html\Widgets\Form\CheckboxField;

/**
 * Base class for rendering simple boolean fields
 * @Component
 * @ApplyTo { "php" :[ "boolean" ] }
 */
class BooleanFieldRenderer extends BaseFieldRenderer implements SingleFieldRendererInterface, ViewFieldRendererInterface {

	/**
	 * Fine key to the text to be displayed in 'view' mode when value is 'true'
	 * @var string
	 */
	public $viewKeyIfTrue;
	
	/**
	 * Fine key to the text to be displayed in 'view' mode when value is 'false'
	 * @var string
	 */
	public $viewKeyIfFalse;
	
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::renderEdit()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptorInstance->getFieldName();
		
		$checkboxField = new CheckboxField($descriptorInstance->fieldDescriptor->getFieldLabel(), $fieldName, '1', $descriptorInstance->getFieldValue());
		ob_start();
		$checkboxField->toHtml();
		return ob_get_clean();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::renderView()
	 */
	public function renderView($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$valueIfTrue = $this->viewKeyIfTrue ? iMsg($this->viewKeyIfTrue) : "Yes";
		$valueIfFalse = $this->viewKeyIfFalse ? iMsg($this->viewKeyIfFalse) : "No";
		return "<span id='".$descriptorInstance->getFieldName()."-view-field'>".( $descriptorInstance->getFieldValue() ? $valueIfTrue : $valueIfFalse )."</span>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::getJSEdit()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		return array();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::getJSView()
	 */
	public function getJSView($descriptor, $bean, $id){
		return array();
	}
	
}