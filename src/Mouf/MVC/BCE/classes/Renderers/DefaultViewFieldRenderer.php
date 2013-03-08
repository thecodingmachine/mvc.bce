<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\BaseFieldDescriptor;
use Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface;

abstract class DefaultViewFieldRenderer extends BaseFieldRenderer implements ViewFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::renderView()
	 */
	function renderView($descriptor){
		/* @var $descriptor BaseFieldDescriptor */
		return "<span id='".$descriptor->getFieldName()."-view-field'>".$descriptor->getFieldValue()."</span>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::getJSView()
	 */
	function getJSView($descriptor){
		return array();
	}
	
}