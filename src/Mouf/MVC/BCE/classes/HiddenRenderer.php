<?php
namespace Mouf\MVC\BCE\classes;

/**
 * This renderer handles hidden input fields
 * @ApplyTo { "pk" : [ "pk" ] }
 * @Component
 */
class HiddenRenderer implements SingleFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function render($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$value = $descriptor->getFieldValue();
		return "<input type='hidden' value='".userinput_to_htmlprotected($value)."' name='".$fieldName."' id='".$fieldName."'/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJS($descriptor){
		return array();
	}
	
}