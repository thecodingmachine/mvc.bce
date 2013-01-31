<?php
use Mouf\MVC\BCE\Classes\Descriptors\BaseFieldDescriptor;
use Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface;

class DefaultViewFieldRenderer implements ViewFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::renderView()
	 */
	function renderView($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		return "<span id='".$descriptor->getFieldName()."-view-field'>".$descriptor->getFieldValue()."</span>";
	}
	
}