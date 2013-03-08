<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

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
	public function renderEdit($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$value = $descriptor->getFieldValue();
		return "<input type='text' value='".$value."' name='".$fieldName."' id='".$fieldName."'/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJSEdit($descriptor){
		return array();
	}
	
}