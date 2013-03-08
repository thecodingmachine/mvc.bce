<?php
namespace Mouf\MVC\BCE\Classes\Renderers;


/**
 * Base class for rendering simple text fields
 * @Component
 */
class PasswordFieldRenderer extends BaseFieldRenderer implements SingleFieldRendererInterface, ViewFieldRendererInterface {
	
	/**
	 * Autocomplete the field in the form.
	 * 
	 * @Property
	 * @var boolean
	 */
	public $autocomplete = false;
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$value = $descriptor->getFieldValue();
		return "<input type='password' ".($this->autocomplete ? "autocomplete='on'" : "autocomplete='off'")." value='' name='".$fieldName."' id='".$fieldName."'/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor){
		return array();
	}
	
	public function renderView($descriptor){
		return false;
	}
	
	public function getJSView($descriptor){
		return array();
	}
	
}