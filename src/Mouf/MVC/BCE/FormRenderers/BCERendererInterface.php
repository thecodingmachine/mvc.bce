<?php
namespace Mouf\MVC\BCE\FormRenderers;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

use Mouf\MVC\BCE\BCEForm;
/**
 * This interface is implemented by all form renderers
 * @Component
 */
interface BCERendererInterface {
	
	/**
	 * Main function of the Renderer: output the form's HTML
	 * @param BCEForm $fieldDescriptors
	 * @param array<FieldDescriptorInstance> $fieldDescriptors
	 * @param FieldDescriptorInstance $idDescriptorInstance
	 */
	public function render(BCEForm $form, $descriptorInstances,	FieldDescriptorInstance $idDescriptorInstance);
	
	/**
	 * Returns the css stylesheet depending on the chosen skin
	 * @return WebLibrary
	 */
	public function getSkin();
	
}