<?php
namespace Mouf\MVC\BCE\classes;


/**
 * Base class for rendering simple text area fields
 * @Component
 */
class TextAreaFieldRenderer implements SingleFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function render($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$value = $descriptor->getFieldValue();
		return '<textarea name="'.$fieldName.'" id="'.$fieldName.'">'.userinput_to_htmlprotected($value).'</textarea>';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJS($descriptor){
		return array();
	}
	
}