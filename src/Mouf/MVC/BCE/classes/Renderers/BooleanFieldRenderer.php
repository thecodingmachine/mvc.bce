<?php
namespace Mouf\MVC\BCE\Classes\Renderers;
use Mouf\MVC\BCE\Classes\Descriptors\BaseFieldDescriptor;

/**
 * Base class for rendering simple boolean fields
 * @Component
 * @ApplyTo { "php" :[ "boolean" ] }
 */
class BooleanFieldRenderer implements SingleFieldRendererInterface, ViewFieldRendererInterface {

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
	public function renderEdit($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$strChecked = $descriptor->getFieldValue() ? "checked = 'checked'" : "";
		return "<input type='checkbox' value='1' name='$fieldName' id='$fieldName' $strChecked/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::renderView()
	 */
	public function renderView($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		$valueIfTrue = $this->viewKeyIfTrue ? iMsg($this->viewKeyIfTrue) : "Yes";
		$valueIfFalse = $this->viewKeyIfFalse ? iMsg($this->viewKeyIfFalse) : "No";
		return "<span id='".$descriptor->getFieldName()."-view-field'>".( $descriptor->getFieldValue() ? $valueIfTrue : $valueIfFalse )."</span>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::getJSEdit()
	 */
	public function getJSEdit($descriptor){
		return array();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::getJSView()
	 */
	public function getJSView($descriptor){
		return array();
	}
	
}