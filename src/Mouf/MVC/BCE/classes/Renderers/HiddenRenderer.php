<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

/**
 * This renderer handles hidden input fields
 * @ApplyTo { "pk" : [ "pk" ] }
 * @Component
 */
class HiddenRenderer extends BaseFieldRenderer implements SingleFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$value = $descriptor->getFieldValue();
		return "<input type='hidden' value='".$value."' name='".$fieldName."' id='".$fieldName."'/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor){
		return array();
	}
	
}