<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\Html\Utils\WebLibraryManager\WebLibraryManager;
use Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface;

use Mouf\MVC\BCE\Descriptors\FieldDescriptorInstance;
/**
 * This interface is implemented by any field renderer
 *
 */
interface EditFieldRendererInterface {
	
	/**
	 * Main function of the FieldRenderer : return field's HTML code
	 * @param FieldDescriptorInstance $descriptor
	 * @return string
	 */
	public function renderEdit($descriptorInstance);
	
	
	/**
	 * Function that may return some JS script for Edit mode
	 * @param BCEFieldDescriptorInterface $descriptor
	 * @return array<string>
	 */
	public function getJSEdit(BCEFieldDescriptorInterface $descriptor, $bean, $id, WebLibraryManager $webLibraryManager);
	
	
}