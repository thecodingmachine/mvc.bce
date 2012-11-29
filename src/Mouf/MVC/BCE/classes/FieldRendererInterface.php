<?php
namespace Mouf\MVC\BCE\classes;

/**
 * This interface is implemented by any field renderer
 *
 */
interface FieldRendererInterface {
	
	/**
	 * Main function of the FieldRenderer : return field's HTML code
	 * @param FieldDescriptor $descriptor
	 * @return string
	 */
	public function render($descriptor);
	
	/**
	 * Function that may return some JS script (eg. datepicker, slider, etc...) related to this renderer
	 * @param FieldDescriptor $descriptor
	 * @return array<string>
	 */
	public function getJS($descriptor);
	
}