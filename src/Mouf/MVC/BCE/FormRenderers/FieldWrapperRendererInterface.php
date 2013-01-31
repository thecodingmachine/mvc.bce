<?php
namespace Mouf\MVC\BCE\FormRenderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;

interface FieldWrapperRendererInterface {
	
	/**
	 * Renders the field frapper (including label and eventuallt description)
	 * @param FieldDescriptor $descriptor
	 * @param string $fieldHtml the HTML of the field (ie <input .../>, <select>...</select>, ...)
	 * @param string $formMode the mode (edit or view) of the form
	 */
	public function render(FieldDescriptor $descriptor, $fieldHtml, $formMode);
	
}