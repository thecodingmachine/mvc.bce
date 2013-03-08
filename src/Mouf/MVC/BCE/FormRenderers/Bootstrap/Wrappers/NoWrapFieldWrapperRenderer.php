<?php
namespace Mouf\MVC\BCE\FormRenderers\Bootstrap\Wrappers;

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
	public function render(FieldDescriptor $descriptor, $fieldHtml, $formMode) {
		echo $descriptor->getRenderer()->render($descriptor, $formMode); 
	}
	
}