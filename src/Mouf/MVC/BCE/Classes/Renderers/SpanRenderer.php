<?php
namespace Mouf\MVC\BCE\Classes\Renderers;
 
use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;


/**
 * This renderer handles Read-Only fields
 * @ApplyTo { "pk" : [ "pk" ] }
 * @Component
 */
class SpanRenderer extends DefaultViewFieldRenderer implements SingleFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptorInstance->getFieldName();
		$value = $descriptorInstance->getFieldValue();
		return "<span>".$value."</span><input type='hidden' name='" . $fieldName . "' value='" . $value . "' />";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::getJSEdit()
	 */
	public function getJSEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}
	
}