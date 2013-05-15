<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

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
		$strChecked = $descriptorInstance->getFieldValue() ? "checked = 'checked'" : "";
		return "<input type='checkbox' value='1' name='$fieldName' id='$fieldName' $strChecked ".$descriptorInstance->printAttributes()."/>";
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
	public function getJSEdit($descriptorInstance){
		return array();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::getJSView()
	 */
	public function getJSView($descriptorInstance){
		return array();
	}
	
}