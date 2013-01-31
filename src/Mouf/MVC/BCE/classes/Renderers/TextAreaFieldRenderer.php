<?php
namespace Mouf\MVC\BCE\Classes\Renderers;


/**
 * Base class for rendering simple text area fields
 * @Component
 */
class TextAreaFieldRenderer implements SingleFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::render()
	 */
	public function renderEdit($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		$fieldName = $descriptor->getFieldName();
		$value = $descriptor->getFieldValue();
		return '<textarea name="'.$fieldName.'" id="'.$fieldName.'">'.$value.'</textarea>';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see FieldRendererInterface::getJS()
	 */
	public function getJS($descriptor){
		return array();
	}
	
}