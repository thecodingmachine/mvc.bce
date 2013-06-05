<?php
namespace Mouf\MVC\BCE\Classes\Renderers;

use Mouf\MVC\BCE\Classes\ScriptManagers\ScriptManager;

use Mouf\MVC\BCE\Classes\Descriptors\FieldDescriptorInstance;

/**
 * This renderer handles text input fields with the jQuery MiniColor
 * @Component
 */
class ColorPickerRenderer extends BaseFieldRenderer implements SingleFieldRendererInterface, ViewFieldRendererInterface  {
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::renderEdit()
	 */
	public function renderEdit($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptorInstance->getFieldName();
		$value = $descriptorInstance->getFieldValue();
		return "<input type='text' value='".$value."' name='".$fieldName."' id='".$fieldName."' class='color-picker'/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\EditFieldRendererInterface::getJSEdit()
	 */
	public function getJSEdit($descriptor, $bean, $id){
		/* @var $descriptorInstance FieldDescriptorInstance */
		$fieldName = $descriptor->getFieldName();
		return array(
			ScriptManager::SCOPE_READY => "
				function init() {
					// Enabling miniColors
					$('.color-picker[name=\"$fieldName\"]').miniColors({
						change: function(hex, rgb) {
						},
						open: function(hex, rgb) {
						},
						close: function(hex, rgb) {
						}
					});
				}
				init();
			"
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::renderView()
	 */
	public function renderView($descriptorInstance){
		/* @var $descriptorInstance FieldDescriptorInstance */
		return "<input ".$descriptorInstance->printAttributes()." type='text' value='".$descriptorInstance->getFieldValue()."' name='".$descriptorInstance->getFieldValue()."' id='".$descriptorInstance->getFieldValue()."' class='color-picker' disabled='disabled'/>";
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Mouf\MVC\BCE\Classes\Renderers\ViewFieldRendererInterface::getJSView()
	 */
	public function getJSView($descriptor, $bean, $id){
		return $this->getJSEdit($descriptor);
	}
	
	
}