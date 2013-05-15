<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Descriptors\FieldDescriptorInstance;

interface ViewFieldRendererInterface {
	
	/**
	 * Returns the HTML for VIEWING a field's value
	 * @param FieldDescriptorInstance $descriptor
	 * @return string
	 */
	public function renderView($descriptorInstance);
	
	/**
	 * Function that may return some JS script for 'View' mode
	 * @param FieldDescriptorInstance $descriptor
	 * @return array<string>
	 */
	public function getJSView($descriptorInstance);
	
}