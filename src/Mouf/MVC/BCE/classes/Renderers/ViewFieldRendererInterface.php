é<?php
namespace Mouf\MVC\BCE\Classes\Renderers;


interface ViewFieldRendererInterface {
	
	/**
	 * Returns the HTML for VIEWING a field's value
	 * @param FieldDescriptor $descriptor
	 * @return string
	 */
	public function renderView($descriptor);
	
	/**
	 * Function that may return some JS script for 'View' mode
	 * @param FieldDescriptor $descriptor
	 * @return array<string>
	 */
	public function getJSView($descriptor);
	
}