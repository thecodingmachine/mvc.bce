<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

/**
 * This renderer handles Read-Only fields
 * @ApplyTo { "pk" : [ "pk" ] }
 * @Component
 */
class SpanRenderer implements SingleFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function render($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$value = $descriptor->getFieldValue();
		return "<span>".$value."</span><input type='hidden' name='" . $fieldName . "' value='" . $value . "' />";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJS($descriptor){
		return array();
	}
	
}