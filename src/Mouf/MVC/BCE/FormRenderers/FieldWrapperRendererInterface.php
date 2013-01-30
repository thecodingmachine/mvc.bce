<?php
namespace Mouf\MVC\BCE\FormRenderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;

interface FieldWrapperRendererInterface {
	
	/**
	 * renders a field's wrapper
	 * @param FieldDescriptor $descriptor
	 */
	public function render(FieldDescriptor $descriptor) {
		
	}
	
}