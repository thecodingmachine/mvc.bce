<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\Html\Utils\WebLibraryManager\WebLibraryManager;
use Mouf\Html\Widgets\Form\Styles\LayoutStyle;

use Mouf\MVC\BCE\Classes\Descriptors\BCEFieldDescriptorInterface;
use Mouf\MVC\BCE\Descriptors\FieldDescriptorInstance;
/**
 * This interface is implemented by any field renderer
 *
 */
interface FieldRendererInterface {
	
	/**
	 * Main function of the FieldRenderer : return field's HTML code
	 * @param FieldDescriptorInstance $descriptorInstance
	 * @param string $formMode
	 * @return string the text of the rendered field, false if the field should not be rendered in the given mode (ie password field in view mode)
	 */
	public function render($descriptorInstance, $formMode);
	
	/**
	 * Function that may return some JS script (eg. datepicker, slider, etc...) related to this renderer
	 * @param BCEFieldDescriptorInterface $descriptor
	 * @return array<string>
	 */
	public function getJS(BCEFieldDescriptorInterface $descriptor, $formMode, $bean, $id, WebLibraryManager $webLibraryManager);
	
	
	/**
	 * @return LayoutStyle
	 */
	public function getLayout();
	
}