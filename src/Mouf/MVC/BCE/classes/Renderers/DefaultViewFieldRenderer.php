<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;
use Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface;

abstract class DefaultViewFieldRenderer extends BaseFieldRenderer implements ViewFieldRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::renderView()
	 */
	function renderView($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$value = $descriptorInstance->getFieldValue() ? $descriptorInstance->getFieldValue(): " - ";
		return "<span id='".$descriptorInstance->getFieldName()."-view-field'>".$value."</span>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::getJSView()
	 */
	function getJSView($descriptorInstance){
		return array();
	}
	
}