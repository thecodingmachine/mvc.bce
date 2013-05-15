<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

/**
 * Base class for rendering simple text fields
 * @Component
 * @ApplyTo { "php" :[ "string", "int", "number"] }
 */
class TextFieldRenderer extends DefaultViewFieldRenderer implements SingleFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptorInstance->getFieldName();
		$value = $descriptorInstance->getFieldValue();
		$strReadonly = ! $descriptorInstance->fieldDescriptor->canEdit() ? "readonly='readonly'" : "";
		return "<input type='text' value='".$value."' name='".$fieldName."' id='".$fieldName."' $strReadonly ".$descriptorInstance->printAttributes()."/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return array();
	}
	
}