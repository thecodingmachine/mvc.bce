<?php
namespace Mouf\MVC\BCE\FormRenderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

use Mouf\MVC\BCE\FormRenderers\DescriptionRendererInterface;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;

use Mouf\MVC\BCE\FormRenderers\FieldWrapperRendererInterface;

/**
 * Base class for wrapping simple fields
 * 
 * @ApplyTo { "pk" : [ "pk" ] }
 */

class NoWrapFieldWrapperRenderer implements FieldWrapperRendererInterface {
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\FormRenderers\FieldWrapperRendererInterface::render()
	 */
	public function render($descriptorInstance, $fieldHtml, $formMode) {
		echo $descriptorInstance->fieldDescriptor->toHTML($descriptorInstance, $formMode); 
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\FormRenderers\FieldWrapperRendererInterface::setDescriptionRenderer()
	 */
	public function setDescriptionRenderer(DescriptionRendererInterface $renderer){
		return;
	}
	
}