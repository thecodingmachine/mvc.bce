<?php
namespace Mouf\MVC\BCE\FormRenderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptor;

interface SubFormFieldWrapperRendererInterface {
	
	/**
	 * Renders the field frapper (including label and eventuallt description)
	 * @param FieldDescriptorInstance $descriptorInstance
	 * @param string $fieldHtml the HTML of the field (ie <input .../>, <select>...</select>, ...)
	 * @param string $formMode the mode (edit or view) of the form
	 */
	public function render($descriptorInstance, $fieldHtml, $formMode);
	
}