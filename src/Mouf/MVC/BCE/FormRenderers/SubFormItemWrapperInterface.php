<?php
namespace Mouf\MVC\BCE\FormRenderers;

use Mouf\MVC\BCE\BCEFormInstance;
use Mouf\MVC\BCE\Classes\Descriptors\SubFormFieldDescriptor;

interface SubFormItemWrapperInterface {
	
	/**
	 * Renders the HTML output for each item of the subform descriptor (i.e. each form instance)
	 * @param SubFormFieldDescriptor $desc the descriptor that handles the subforms
	 * @param BCEFormInstance $formInstance the form instance (i.e. the current item)
	 * @param string $index
	 */
	public function toHtml(SubFormFieldDescriptor $desc, BCEFormInstance $formInstance, $index = "");
	
	/**
	 * Returns the javscript code and scope
	 * @return array<string, string> the scope of the script as key and the script itself as value
	 */
	public function getRemoveItemJS();
	
}